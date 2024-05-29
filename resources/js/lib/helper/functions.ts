import { useThemeStore } from "@/modules/theme";
import { Caret } from "./caret";
import { computed } from "vue";
import { storeToRefs } from "pinia";
export function debounce(fn: any, wait: number) {
  let timer: ReturnType<typeof setTimeout> | null = null;
  if (timer) clearTimeout(timer);
  timer = setTimeout(() => {
    if (typeof fn === "function") {
      fn();
    }
  }, wait);
}

export function placeCaretAtEnd(el: any) {
  el.focus();
  const body: any = document.body;
  if (
    typeof window.getSelection != "undefined" &&
    typeof document.createRange != "undefined"
  ) {
    var range = document.createRange();
    range.selectNodeContents(el);
    range.collapse(false);
    var sel: any = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(range);
  } else if (typeof body.createTextRange != "undefined") {
    var textRange = body.createTextRange();
    textRange.moveToElementText(el);
    textRange.collapse(false);
    textRange.select();
  }
}

export function pagination(
  page: number,
  perPage: number,
  total: number,
  display: number = 5
) {
  const pages = Math.ceil(total / perPage);
  const out: any = [];
  for (let p = 1; p <= pages; p++) {
    if (pages <= display) {
      out.push(p);
    } else {
      let prefix = Math.floor(display / 2);
      let suffix = Math.floor(display / 2);
      if (display % 2 === 0) {
        prefix--;
      }
      if (page <= prefix) {
        suffix += prefix - page + 1;
        prefix = page;
      } else if (pages < page + suffix) {
        const diff = page + suffix - pages;
        suffix -= diff;
        prefix += diff;
      }
      if (p === page || (p >= page - prefix && p <= page + suffix)) {
        out.push(p);
      }
    }
  }
  return out;
}

export function isPromise(obj: any): boolean {
  return !!obj && typeof obj.then === "function";
}

export function caretPosition(field: any) {
  if (field) {
    return new Caret(field).getPos();
  }
  return 0;
}

export function setCaretPosition(field: any, pos: number) {
  if (field) {
    new Caret(field).setPos(pos);
  }
}

export function gcd(a, b) {
  if (b < 0.0000001) return a;
  return gcd(b, Math.floor(a % b));
}

export function reverseAspectRatio(fraction) {
  const len = fraction.toString().length - 2;
  let denominator = Math.pow(10, len);
  let numerator = fraction * denominator;

  const divisor = gcd(numerator, denominator);

  numerator /= divisor;
  denominator /= divisor;
  return Math.floor(denominator) / Math.floor(numerator);
}

export function meterToPixel(meter: number) {
  const pixels = 3779.5275591;
  return meter * pixels;
}

export function cutImage(
  image: HTMLImageElement,
  left: number,
  top: number,
  width: number,
  height: number,
  format: string = "image/png",
  spaceLeft: number = 0,
  spaceTop: number = 0
) {
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");
  canvas.width = width;
  canvas.height = height;
  ctx?.drawImage?.(
    image,
    left,
    top,
    width,
    height,
    spaceLeft,
    spaceTop,
    width,
    height
  );
  const base64 = canvas.toDataURL(format);
  canvas.remove();
  return base64;
}

export function zoomImage(
  image: HTMLImageElement,
  width: number,
  height: number,
  format: string = "image/png"
) {
  const canvas = document.createElement("canvas");
  canvas.width = width;
  canvas.height = height;
  const context = canvas.getContext("2d");
  if (context) {
    context.drawImage(
      image,
      0,
      0,
      image.naturalWidth,
      image.naturalHeight,
      0,
      0,
      canvas.width,
      canvas.height
    );
    const base64 = canvas.toDataURL(format);
    canvas.remove();
    return base64;
  }
  return image.src;
}
