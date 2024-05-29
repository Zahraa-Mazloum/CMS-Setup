import { App, createApp, h, nextTick } from "vue";

export const createComponent = <
  Props extends Record<string, unknown>,
  Expose extends Record<string, unknown>,
  Slots extends Record<string, unknown>,
>(
  component: any
) => {
  return (props: {} | Props = {}, slots: {} | Slots = {}): Promise<Expose> => {
    return new Promise<Expose>((resolve) => {
      const mountElement = document.createElement("div");
      document.body.appendChild(mountElement);
      const app: App<Element> = createApp(
        {
          components: { component },
          data() {
            return {
              active: {
                type: Boolean,
                default: () => true,
              },
            };
          },
          mounted() {
            Object.keys(this.$refs.wrapper).forEach((name: string) => {
              this[name] = this.$refs.wrapper[name];
            });
          },
          render() {
            if (this.active) {
              return h(
                component,
                { ...props, controller: this, ref: "wrapper" },
                slots
              );
            }
          },
          methods: {
            removeController() {
              this.active = false;
              this.$nextTick(() => {
                app.unmount();
                mountElement?.parentElement?.removeChild?.(mountElement);
              });
            },
          },
        },
        {}
      );
      const instance = app.mount(mountElement);
      nextTick(() => {
        resolve(instance as any);
      });
    });
  };
};
