import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import { VitePWA } from "vite-plugin-pwa";
import { md5 } from "md5js";
const { resolve } = require("path");
const fs = require("fs");

const { jawdatPlugin } = require("./resources/js/dev/vite/jawdat-plugin");

export default defineConfig(async (args) => {
  const [mode, tier] = args.mode.split(":");
  const isDev = mode === "development";
  const isProd = mode === "production";
  const directoryName = isProd ? "prod" : "dev";
  const env = loadEnv(mode, __dirname, "");
  const tiers = env.TIERS.split(",");

  const regex = /resources\/js\/tiers\/(.*)\/startup\/(.*)\.(.*)/gm;

  return {
    mode,
    root: __dirname,
    base: `./`,
    publicDir: false,
    cacheDir: `${resolve(__dirname, "./runtime/.vite")}`,
    appType: "custom",
    envPrefix: "CND_",
    clearScreen: isProd,
    logLevel: "info",
    resolve: {
      alias: {
        "@bs": `${resolve(__dirname, "./node_modules/bootstrap/scss")}`,
        "@node_modules": `${resolve(__dirname, "./node_modules")}`,
        "@apps": `${resolve(__dirname, "./resources/js/tiers")}`,
        "@scss": `${resolve(__dirname, "./resources/css")}`,
        "@": `${resolve(__dirname, "./resources/js")}`,
      },
    },
    server: {
      hmr: {
        overlay: false,
      },
    },
    build: {
      cssCodeSplit: false,
      cssMinify: true,
      rollupOptions: {
        output: {
          entryFileNames: (chunkInfo) => {
            const names = chunkInfo.name.split("_");
            if (names.length > 1) {
              const tierName = names[0];
              if (tiers.includes(tierName)) {
                names.splice(0, 1);
                const name = names.join("_");
                return `apps/${name}.[hash].js`;
              }
            }
            return "[name].[hash].js";
          },
          assetFileNames: (assetInfo) => {
            if (assetInfo.name === "style.css") {
              return `main.css`;
            }
            return assetInfo.name.replace(
              regex,
              (phrase, tier, fileName, ext) => {
                if (assetInfo.name.includes(`/${env.DEFAULT_TIER}/`)) {
                  return `[ext]/${fileName.toLowerCase()}.${ext}`;
                }
                return `[ext]/${tier}/${fileName.toLowerCase()}.${ext}`;
              }
            );
          },
          chunkFileNames: (dataInfo) => {
            return `chunk/${dataInfo.name}.[hash].js`;
          },
          manualChunks: (id) => {
            if (id.includes("node_modules")) {
              const name = id.split("/node_modules/.pnpm/")[1].split("/")[0];
              return `m${md5(name)}`;
            }
          },
          compact: isProd,
        },
      },
      target: "modules",
      manifest: '__meta.json',
      minify: "esbuild",
      outDir: `${resolve(__dirname, `./public/${directoryName}/${tier}`)}`,
      assetsDir: `${resolve(__dirname, `./public/${directoryName}/assets`)}`,
      sourcemap: isDev,
    },
    css: {
      devSourcemap: isDev,
      preprocessorOptions: {
        scss: {
          quietDeps: true,
          sourceMap: true,
          sourceMapIncludeSources: true,
        }
      },
    },
    plugins: [
      jawdatPlugin({
        rootPath: __dirname,
        tier,
        mode: directoryName,
        env,
      }),
      vue({
        template: {
          compilerOptions: {
            isCustomElement: (tag) => tag.startsWith('web-')
          }
        },
        script: {
          propsDestructure: true,
          defineModel: true,
        },
        style: {
          trim: true,
        },
      }),
      VitePWA({
        mode,
        strategies: "generateSW",
        registerType: "prompt",
        devOptions: {
          enabled: isDev,
          type: "classic",
          navigateFallback: null,
        },
        workbox: {
          globPatterns: ["*/*.{js,css,jpg,png,svg,eot,ttf,woff,woff2}"],
          cleanupOutdatedCaches: true,
          navigateFallback: null,
        },
        includeAssets: ["favicon.ico", "robots.txt", "*.svg", "*.{png,ico}"],
        manifest: require(resolve(__dirname, "./public/manifest.json")),
        manifestFilename: "manifest.json",
        outDir: "public",
        filename: `sw-${directoryName}.js`,
        minify: isProd,
      }),
    ],
    define: {
      __APP_VERSION__: "1",
      __VUE_PROD_DEVTOOLS__: !isProd,
    },
  };
});
