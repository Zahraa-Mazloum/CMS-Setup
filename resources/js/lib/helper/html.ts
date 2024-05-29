import { onMounted, onUnmounted } from "vue";

declare var document: Document

export function createElement(html: string): ChildNode | null {
  const template = document.createElement("template");
  html = html.trim();
  template.innerHTML = html;
  return template.content.firstChild;
}

export function createElementIfNotExists(selector: string, html: string, appendTo = "body"): Element | null {
  let el: Element | null = document.querySelector(selector);
  if (!el) {
    document.querySelector(appendTo)?.insertAdjacentHTML("afterbegin", html);
    el = document.querySelector(selector);
  }
  return el;
}

type StylePropertyOptions = {
    int?: boolean
    float?: boolean
}

export function getStyleProperty(element: Element, property: string, options: StylePropertyOptions = {}) {
  const cs = getComputedStyle(element);
  let value: any = cs.getPropertyValue(property);
  if (value) {
    if (options.float) {
      value = parseFloat(value.replace("px", ""));
    }
    if (options.int) {
      value = parseInt(value.replace("px", ""));
    }
  }
  return value;
}

export function borderHeight(element: Element) {
  return getStyleProperty(element, "border-top-width", { int: true }) + getStyleProperty(element, "border-bottom-width", { int: true });
}

export function paddingHeight(element: Element) {
  return getStyleProperty(element, "padding-top", { int: true }) + getStyleProperty(element, "padding-bottom", { int: true });
}

export function contentHeight(element: Element) {
  return element.clientHeight - borderHeight(element) - paddingHeight(element);
}

export function keypress(element: any, onDown: null | ((event: any) => void) = null, onUp: null | ((event: any) => void) = null) {
  const down = (e: any) => {
    onDown && onDown(e);
  };

  const up = (e: any) => {
    onUp && onUp(e);
  };

  onMounted(() => {
    element.addEventListener("keydown", down);
    element.addEventListener("keyup", up);
  });

  onUnmounted(() => {
    element.removeEventListener("keydown", down);
    element.removeEventListener("keyup", up);
  });
}
