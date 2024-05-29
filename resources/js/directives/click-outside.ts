const directive = {
  beforeMount(el: any, binding: any) {
    const doc = binding.arg ?? document;
    el.clickOutsideEvent = function (event: any) {
      if (
        !(el === event.target || el.contains(event.target)) &&
        binding.value
      ) {
        binding.value(event, el);
      }
    };
    doc.addEventListener("click", el.clickOutsideEvent);
    doc.addEventListener("touchstart", el.clickOutsideEvent);
  },
  unmounted(el: any, binding: any) {
    const doc = binding.arg ?? document;
    doc.removeEventListener("click", el.clickOutsideEvent);
    doc.removeEventListener("touchstart", el.clickOutsideEvent);
  },
};

export const vClickOutside = directive;
