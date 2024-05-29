import chalk from "chalk";
import { scanAppComponents } from "../lib/pre-vue";
const fs = require("fs");

export function jawdatPlugin({
  rootPath,
  tier,
  mode,
  env = {},
  extraInputs = {},
}) {
  const ids = [];

  return {
    name: "jawdatPlugin",
    config: async (config, { command }) => {
      const entries = {
        ...(await scanAppComponents(rootPath, [tier], env)),
        ...extraInputs,
      };

      console.log("\n", chalk.greenBright.bold("Entries Files"), " : \n");
      const keys = Object.keys(entries);
      let max = 0;
      keys.forEach((key) => {
        const length = key.length;
        if (length > max) {
          max = length;
        }
      });
      keys.forEach((key, i) => {
        console.log(
          chalk.blue(i + 1),
          " - ",
          chalk.yellow.bold(key.padEnd(max, " ")),
          " : ",
          chalk.gray.italic(entries[key])
        );
      });
      console.log("\n\n");

      config.entries = entries;
      if (config.build && config.build.rollupOptions) {
        config.build.rollupOptions.input = entries;
      }
    },
    configResolved(resolvedConfig) {},
    options(options) {},
    buildStart(options) {},
    resolveId(source, importer, options) {},
    load(id) {
      ids.push(id);
    },
    transform(code, id) {
      if (id.includes("?vue&type=style")) {
        return {
          code: `@layer components { ${code} }`,
          map: null,
        };
      } else if (id.includes("node_modules") && id.includes(".css")) {
        return {
          code: `@layer components { ${code} }`,
          map: null,
        };
      }
      return {
        code,
        map: null,
      };
    },
    buildEnd(error) {},
    closeBundle() {
      const filename = `${rootPath}/public/${mode}/${tier}/main.css`;
      let css = fs.readFileSync(filename, { encoding: "utf8", flag: "r" });
      css = css.replace(
        '@charset "UTF-8";',
        '@charset "UTF-8";@layer bootstrap, preLayout, layout, components, end;'
      );
      fs.writeFileSync(filename, css);
      if (!fs.existsSync(`${rootPath}/runtime/vite`)) {
        fs.mkdirSync(`${rootPath}/runtime/vite`);
      }
      fs.writeFileSync(
        `${rootPath}/runtime/vite/${mode}-${tier}-files.txt`,
        ids.join("\n")
      );
    },
  };
}
