const directive = (el, binding) => {
  let items = [];
  let active = true;

  if (Array.isArray(binding.value)) {
    items = binding.value;
  } else {
    throw new Error("Placeholder value should be an array of integers 1 ~ 12");
  }
  
  if (Object.hasOwn(binding, "arg")) {
    if (binding.arg === "true" || binding.arg === "false") {
      active = binding.arg === "true";
    } else if (typeof binding.arg === "boolean") {
      active = binding.arg;
    }
  }

  if (active) {
    const template = items
      .map((value: number) => {
        return `<span class="placeholder col-${value} me-2"></span>`;
      })
      .join("");
    el.innerHTML = `${template}`;
    el.classList.add("placeholder-glow");
  } else {
    el.classList.remove("placeholder-glow");
  }
};

export const vPlaceholder = directive;
