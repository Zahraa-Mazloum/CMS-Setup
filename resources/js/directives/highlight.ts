const directive = {
  mounted(el: any, binding: any, vnode: any) {
    el.innerHTML = binding.value.text.replace(
      new RegExp(`(${binding.value.query})`, "gim"),
      (text, captured) => {
        if (captured) {
          return `<span class="fw-bold text-primary">${captured}</span>`;
        }
        return "";
      }
    );
  },
};

export const vHighlight = directive;
