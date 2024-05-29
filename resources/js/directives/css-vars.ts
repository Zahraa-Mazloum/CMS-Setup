const directive = {
    beforeMount(el: any, binding: any, vnode: any) {
      Object.keys(binding.value).forEach((name) => {
        el.style.setProperty(name, binding.value[name])
      })
    },
  };
  
  export const vCssVars = directive;
  