import { Tooltip } from "bootstrap";

const tooltips = new Map<HTMLElement, Tooltip>();

const directive = {
  beforeMount(el: HTMLElement, binding: any, vnode: any) {
    let options: any = {};
    if (typeof binding.value === "object") {
      el.setAttribute("title", binding.value?.title ?? null);
      options = { ...options, ...binding.value };
    } else if (typeof binding.value === "string") {
      el.setAttribute("title", binding.value);
      options.title = binding.value;
    }
    tooltips.set(el, new Tooltip(el, options));
  },
  beforeUnmount(el: HTMLElement) {
    tooltips.get(el)?.dispose();
    tooltips.delete(el);
  },
};

export const vTooltip = directive;
