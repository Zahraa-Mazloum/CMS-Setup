import {
	existsSync,
	mkdirSync,
	rmSync,
	writeFileSync,
	readdir,
	stat,
} from "fs";
import { outdent } from "outdent";
const { extname, join, parse, resolve } = require("path");
const promisify = require("util").promisify;
const readdirp = promisify(readdir);
const statp = promisify(stat);

export const scanAppComponents = function (rootPath, tiers = [], env = {}) {
	return new Promise(async (solve) => {
		const preVue = new PreVue(rootPath, tiers, env);
		const list = await preVue.scan();
		solve(list);
	});
};

class PreVue {
	constructor(rootPath, tiers = [], env = {}) {
		this.rootPath = rootPath;
		this.tiers = tiers;
		this.env = env;
	}

	scan() {
		return new Promise(async (solve) => {
			let list = {};
			this.clearStartupDirectory();
			for (const tier of this.tiers) {
				list = await this.scanDirectory(
					`${this.rootPath}/resources/js/tiers/${tier}/apps`,
					tier,
					list
				);
			}
			solve(list);
		});
	}

	async scanDirectory(directoryName, tier = "default", results = {}) {
		this.createDirectoryIfNotExist(directoryName);
		const files = await readdirp(directoryName);
		for (let f of files) {
			const fullPath = join(directoryName, f);
			const ext = extname(fullPath);
			let stat = await statp(fullPath);
			if (stat.isDirectory()) {
				results = await this.scanDirectory(fullPath, tier, results);
			} else if (ext.toLowerCase() === ".vue") {
				const file = parse(fullPath);
				const prefix = `${tier}_`;
				results[`${this.slugify(`${prefix}${f.replace(".vue", "")}`)}`] =
					await this.createVueTsFile(tier, file.name, stat);
			}
		}
		return { [tier]: await this.createCommonFile(tier), ...results };
	}

	createCommonFile(tier) {
		return new Promise(async (done) => {
			const filePath = `${this.rootPath}/resources/js/tiers/${tier}/${tier}.ts`;
			if (!existsSync(filePath)) {
				const projectName = this.env.APP_NAME;
				const options = {
					weekday: "long",
					year: "numeric",
					month: "long",
					day: "numeric",
					hour: "2-digit",
					minute: "2-digit",
					timeZoneName: "short",
				};
				const content = outdent`
					/**
					 * ${tier}.ts Common Script File
					 *
					 * @project ${projectName}
					 * @company Codendot <https://codendot.com>
					 * @author Jawdat Sobh <jawdat@codendot.com>
					 * @since ${new Date().toLocaleDateString(undefined, options)}
					 *
					 */

					import "@scss/tiers/${tier}/${tier}.scss";
					import "@/native";

					(() => {

						function main()
						{
							// TODO...
						}

						document.addEventListener("readystatechange", async (event: any) => {
							if (event.target.readyState === 'complete') {
								(await import("@/modules/theme"))?.initTheme?.()
								main()
							}
						})

					})()
            	`;
				writeFileSync(filePath, content, {
					encoding: "utf8",
				});
			}
			done(filePath);
		});
	}

	createVueTsFile(tier, appName, stat) {
		return new Promise((done) => {
			this.createBootstrapFile(tier, appName, stat);
			const filePath = this.createStartupFile(tier, appName, stat);
			done(filePath);
		});
	}

	createStartupFile(tier, appName, { birthtime }) {
		const options = {
			weekday: "long",
			year: "numeric",
			month: "long",
			day: "numeric",
			hour: "2-digit",
			minute: "2-digit",
			timeZoneName: "short",
		};

		const startupFolder = `${resolve(
			this.rootPath,
			`./resources/js/tiers/${tier}/startup`
		)}`;
		this.createDirectoryIfNotExist(startupFolder);
		const filePath = `${startupFolder}/${appName}.ts`;

		const projectName = this.env.APP_NAME;

		const startupContent = outdent`
            /**
             * ${appName}.vue Startup File
             *
             * @project ${projectName}
             * @company Codendot <https://codendot.com>
             * @author Jawdat Sobh <jawdat@codendot.com>
             * @since ${birthtime.toLocaleDateString(undefined, options)}
             *
             */

            import { createApp } from "@/lib/app"
            import AppComponent from "../apps/${appName}.vue"
            import bootstrap from "../bootstrap/${appName}"

            const app = createApp("${this.slugify(appName)}", AppComponent)
            bootstrap(app)
            app.mount()

        	`;
		writeFileSync(filePath, startupContent, {
			encoding: "utf8",
		});

		return filePath;
	}

	createBootstrapFile(tier, appName, { birthtime }) {
		const options = {
			weekday: "long",
			year: "numeric",
			month: "long",
			day: "numeric",
			hour: "2-digit",
			minute: "2-digit",
			timeZoneName: "short",
		};

		const bootstrapFolder = `${resolve(
			this.rootPath,
			`./resources/js/tiers/${tier}/bootstrap`
		)}`;
		this.createDirectoryIfNotExist(bootstrapFolder);
		const filePath = `${bootstrapFolder}/${appName}.ts`;

		if (!existsSync(filePath)) {
			const projectName = this.env.APP_NAME;

			const bootstrapContent = outdent`
            /**
             * ${appName}.vue Bootstrap File
             *
             * @project ${projectName}
             * @company Codendot <https://codendot.com>
             * @author Jawdat Sobh <jawdat@codendot.com>
             * @since ${birthtime.toLocaleDateString(undefined, options)}
             *
             */
            import { App } from "@/lib/app"

            export default function(app: App){
                // TODO ...
            }
        `;

			writeFileSync(filePath, bootstrapContent, {
				encoding: "utf8",
			});
		}
	}

	clearStartupDirectory() {
		this.tiers.forEach((tier) => {
			const startupFolder = `${resolve(
				this.rootPath,
				`./resources/js/tiers/${tier}/startup`
			)}`;
			if (existsSync(startupFolder)) {
				rmSync(startupFolder, { recursive: true });
			}
		});
	}

	createDirectoryIfNotExist(dir) {
		if (!existsSync(dir)) {
			mkdirSync(dir, { recursive: true });
		}
	}

	slugify(text, delimiter = "-") {
		return text
			.toString()
			.trim()
			.toLowerCase()
			.replace(/\s+/g, delimiter)
			.replace(/[^\w\-]+/g, "")
			.replace(/\-\-+/g, delimiter)
			.replace(/^-+/, "")
			.replace(/-+$/, "");
	}
}
