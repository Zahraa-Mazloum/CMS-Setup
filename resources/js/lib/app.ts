import { EventDispatcher } from "@/lib/event-dispatcher";
import { Base } from "@/lib/base";
import { pinia } from "@/lib/pinia";
import {
  App as VueApp,
  ComponentPublicInstance,
  createApp as createVueApp,
} from "vue";

declare var window: any;

window.codendot = window.codendot ?? {};
window.codendot.vueApps = window.codendot.vueApps ?? {}; 

type Events = {
  init: EventDispatcher<{sender: Application}>;
  start: EventDispatcher<{sender: Application}>;
  mount: EventDispatcher<{sender: Application, vue: VueApp<Element>}>;
  mounted: EventDispatcher<{sender: Application, vue: VueApp<Element>}>;
};

class Application extends Base<Events> {
  constructor(public name: string, public component: any) {
    super("@/lib/Application");
  }

  mount() {
    this.events.init.dispatch({ sender: this });

    window.codendot.vueApps[this.name] = (
      id: string,
      params: { [name: string]: any }
    ): Promise<VueApp<Element>> => {
      return new Promise((resolve) => {
        this.events.start.dispatch({ sender: this });

        const app: VueApp<Element> = createVueApp(this.component, params);

        app.config.globalProperties.$params = params;

        app.provide("app", app);

        // On Warning
        app.config.warnHandler = (
          msg: string,
          instance: ComponentPublicInstance | null,
          trace: string
        ) => {
          // warnInfo(msg, instance, trace)
          console.warn(msg, instance, trace)
        };

        // On Error
        app.config.errorHandler = (
          err: any,
          instance: ComponentPublicInstance | null,
          info: string
        ) => {
          // errorInfo(err, instance, info)
          console.error(err, instance, info)
        };

        app.use(pinia);

        this.events.mount.dispatch({ vue: app, sender: this });

        app.mount(`#${id}`);

        this.events.mounted.dispatch({ vue: app, sender: this });

        resolve(app);
      });
    };
  }
}

export type App = InstanceType<typeof Application>;

export const createApp = (name: string, component: any) => {
  return new Application(name, component);
};
