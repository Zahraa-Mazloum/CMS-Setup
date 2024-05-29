import { ModalController } from "@/modules/bootstrap/controllers/modal-controller";
import { h, nextTick, ref, Ref } from "vue";
import { mdiCheckBold } from "@mdi/js";
import { autoId } from "./helper/strings";
import { translate } from "./metadata";
import { cssHexToRgb } from "@/lib/helper/colors";
import { cutImage, zoomImage, reverseAspectRatio } from "./helper/functions";
import Button from "@/modules/bootstrap/components/Button.vue";
import Icon from "@/components/Icon.vue";

export type Options = {
  aspectRatio?: "free" | number | false;
  minWidth?: number;
  minHeight?: number;
  maxWidth?: number;
  maxHeight?: number;
};

const defaultOptions: Options = {
  aspectRatio: "free",
  minWidth: 100,
  minHeight: 100,
  maxWidth: 99999,
  maxHeight: 99999,
};

export const crop = (url: string, options: Options = {}) => {
  return new Promise(async (resolve) => {
    options = { ...defaultOptions, ...options };
    const canvasRef = ref() as Ref<HTMLCanvasElement>;
    const containerRef = ref() as Ref<HTMLDivElement>;
    const handlerRef = ref() as Ref<HTMLDivElement>;
    const cropping = ref<any>(null);

    const image = new Image();
    image.onload = function () {
      createModal();
    };
    image.src = url;

    let unmount = () => {};
    let value = () => {};

    const start = () => {
      containerRef.value.style.height = `${Math.ceil(
        (image.naturalHeight * containerRef.value.clientWidth) /
          image.naturalWidth
      )}px`;
      containerRef.value.style.background = `url(${url}) no-repeat top left / cover`;

      handlerRef.value.style.background = `url(${url}) no-repeat top left / ${containerRef.value.clientWidth}px ${containerRef.value.clientHeight}px`;

      canvasRef.value.width = containerRef.value.clientWidth;
      const result = canvasCropper(
        canvasRef.value,
        image,
        handlerRef.value,
        options as Required<Options>
      );
      unmount = result.unmount;
      value = result.value;
    };

    const loaded = () => {
      nextTick(() => {
        setTimeout(() => {
          start();
        }, 100);
      });
    };

    const createModal = async () => {
      const id = autoId("crop");
      const modal = await ModalController(
        {
          noHeader: true,
          closeText: translate("Cancel"),
          padding: 0,
          size: "xl",
          onOpened: () => {
            document.querySelector(`#${id}`)?.addEventListener("click", () => {
              cropping.value?.start?.();
              modal.close();
              resolve(value());
            });
          },
          onMounted: () => {
            loaded();
          },
          onClosed: () => {
            unmount();
          },
        },
        {
          default: () =>
            h(
              "div",
              { class: "position-relative overflow-hidden", ref: containerRef },
              [
                h("div", {
                  class: "position-absolute d-block top-0 start-0 w-100 h-100",
                  style: {
                    background: "rgba(0,0,0,0.5)",
                    zIndex: 1,
                  },
                }),
                h("div", {
                  ref: handlerRef,
                  class: "position-absolute d-block",
                  style: {
                    zIndex: 2,
                  },
                }),
                h("canvas", {
                  ref: canvasRef,
                  class: "position-absolute top-0 start-0",
                  style: { zIndex: 3 },
                }),
              ]
            ),

          buttons: () => [
            h(
              Button,
              {
                class: ["btn", "btn-primary", "text-light"],
                id,
                ref: cropping,
                spinnerColor: "light",
                spinnerSmall: true,
              },
              [
                h(Icon, { path: mdiCheckBold, color: "light" }, ""),
                translate("Crop"),
              ]
            ),
          ],
        }
      );

      modal.open();
    };
  });
};

const canvasCropper = (
  canvas: HTMLCanvasElement,
  image: HTMLImageElement,
  handler: HTMLDivElement,
  options: Required<Options>
) => {
  const ctx = canvas.getContext("2d") as CanvasRenderingContext2D;
  const imageWidth = image.naturalWidth;
  const imageHeight = image.naturalHeight;
  const rect: any = {};
  const color = "#C8A046";
  const handleRadius = 10;

  let dragTL = false;
  let dragBL = false;
  let dragTR = false;
  let dragBR = false;
  let dragWholeRect = false;
  let mouseX = 0;
  let mouseY = 0;
  let startX = 0;
  let startY = 0;

  const ratioWidth = canvas.width / imageWidth;
  const ratioHeight = canvas.height / imageHeight;

  const aspectRatio =
    options.aspectRatio === "free"
      ? options.aspectRatio
      : reverseAspectRatio(options.aspectRatio);
  const minWidth = Math.ceil((options.minWidth * canvas.width) / imageWidth);
  const minHeight = Math.ceil(
    (options.minHeight * canvas.height) / imageHeight
  );
  const maxWidth = Math.ceil((options.maxWidth * canvas.width) / imageWidth);
  const maxHeight = Math.ceil(
    (options.maxHeight * canvas.height) / imageHeight
  );

  canvas.height = Math.ceil((canvas.width * imageHeight) / imageWidth);

  function calculateRectangleHeight(width: number) {
    return aspectRatio === "free" ? width : aspectRatio * width;
  }

  function initRect() {
    rect.width = minWidth;
    rect.height = aspectRatio === "free" ? minHeight : aspectRatio * minWidth;
    rect.top = 0;
    rect.left = 0;
    drawRectInCanvas();
  }

  function drawCircle(x, y, radius) {
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.arc(x, y, radius, 0, 2 * Math.PI);
    ctx.fill();
  }

  function drawHandles() {
    drawCircle(rect.left, rect.top, handleRadius);
    drawCircle(rect.left + rect.width, rect.top, handleRadius);
    drawCircle(rect.left + rect.width, rect.top + rect.height, handleRadius);
    drawCircle(rect.left, rect.top + rect.height, handleRadius);
  }

  function drawRectInCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();
    ctx.lineWidth = 3;
    ctx.strokeStyle = cssHexToRgb(color, 0.7);
    ctx.rect(rect.left, rect.top, rect.width, rect.height);
    ctx.stroke();
    drawHandles();
    handler.style.left = `${rect.left}px`;
    handler.style.top = `${rect.top}px`;
    handler.style.width = `${rect.width}px`;
    handler.style.height = `${rect.height}px`;
    handler.style.backgroundPositionX = `-${rect.left}px`;
    handler.style.backgroundPositionY = `-${rect.top}px`;
  }

  function checkInRect(x, y, r) {
    return (
      x > r.left + 10 &&
      x < r.width + r.left - 10 &&
      y > r.top + 10 &&
      y < r.top + r.height - 10
    );
  }

  function checkCloseEnough(p1, p2) {
    return Math.abs(p1 - p2) < handleRadius;
  }

  function getMousePos(canvas, evt) {
    var clx, cly;
    if (evt.type == "touchstart" || evt.type == "touchmove") {
      clx = evt.touches[0].clientX;
      cly = evt.touches[0].clientY;
    } else {
      clx = evt.clientX;
      cly = evt.clientY;
    }
    var boundingRect = canvas.getBoundingClientRect();
    return {
      x: clx - boundingRect.left,
      y: cly - boundingRect.top,
    };
  }

  function mouseDown(e) {
    const pos = getMousePos(canvas, e);
    mouseX = pos.x;
    mouseY = pos.y;
    // 0. inside movable rectangle
    if (checkInRect(mouseX, mouseY, rect)) {
      dragWholeRect = true;
      startX = mouseX;
      startY = mouseY;
      canvas.style.cursor = "move";
    }
    // 1. top left
    else if (
      checkCloseEnough(mouseX, rect.left) &&
      checkCloseEnough(mouseY, rect.top)
    ) {
      dragTL = true;
      canvas.style.cursor = "nw-resize";
    }
    // 2. top right
    else if (
      checkCloseEnough(mouseX, rect.left + rect.width) &&
      checkCloseEnough(mouseY, rect.top)
    ) {
      dragTR = true;
      canvas.style.cursor = "ne-resize";
    }
    // 3. bottom left
    else if (
      checkCloseEnough(mouseX, rect.left) &&
      checkCloseEnough(mouseY, rect.top + rect.height)
    ) {
      dragBL = true;
      canvas.style.cursor = "ne-resize";
    }
    // 4. bottom right
    else if (
      checkCloseEnough(mouseX, rect.left + rect.width) &&
      checkCloseEnough(mouseY, rect.top + rect.height)
    ) {
      dragBR = true;
      canvas.style.cursor = "nw-resize";
    }
    // (5.) none of them
    else {
      // handle not resizing
      canvas.style.cursor = "auto";
    }
    drawRectInCanvas();
  }

  function mouseMove(e) {
    const pos = getMousePos(canvas, e);
    mouseX = pos.x;
    mouseY = pos.y;
    if (dragWholeRect) {
      e.preventDefault();
      e.stopPropagation();
      const dx = mouseX - startX;
      const dy = mouseY - startY;
      if (rect.left + dx > 0 && rect.left + dx + rect.width < canvas.width) {
        rect.left += dx;
      }
      if (rect.top + dy > 0 && rect.top + dy + rect.height < canvas.height) {
        rect.top += dy;
      }
      startX = mouseX;
      startY = mouseY;
    } else if (dragTL) {
      e.preventDefault();
      e.stopPropagation();
      const newWidth = Math.abs(rect.left + rect.width - mouseX);
      const newHeight = calculateRectangleHeight(newWidth);
      const left = rect.left + rect.width - newWidth;
      const top = rect.height + rect.top - newHeight;
      if (newWidth >= minWidth && left >= 0 && top >= 0) {
        rect.left = left;
        rect.top = top;
        rect.width = newWidth;
        rect.height = newHeight;
      }
    } else if (dragTR) {
      e.preventDefault();
      e.stopPropagation();
      const newWidth = Math.abs(mouseX - rect.left);
      const newHeight = calculateRectangleHeight(newWidth);
      const top = rect.height + rect.top - newHeight;
      const right = rect.left + newWidth;
      if (newWidth >= minWidth && top >= 0 && right <= canvas.width) {
        rect.top = top;
        rect.width = newWidth;
        rect.height = newHeight;
      }
    } else if (dragBL) {
      e.preventDefault();
      e.stopPropagation();
      const newWidth = Math.abs(rect.left + rect.width - mouseX);
      const newHeight = calculateRectangleHeight(newWidth);
      const left = rect.left + rect.width - newWidth;
      const bottom = rect.top + newHeight;
      if (newWidth >= minWidth && left >= 0 && bottom <= canvas.height) {
        rect.left = left;
        rect.width = newWidth;
        rect.height = newHeight;
      }
    } else if (dragBR) {
      e.preventDefault();
      e.stopPropagation();
      const newWidth = Math.abs(rect.left - mouseX);
      const newHeight = calculateRectangleHeight(newWidth);
      const right = rect.left + newWidth;
      const bottom = rect.top + newHeight;
      if (
        newWidth >= minWidth &&
        right <= canvas.width &&
        bottom <= canvas.height
      ) {
        rect.width = newWidth;
        rect.height = newHeight;
      }
    } else {
      cursorIn(e);
    }
    drawRectInCanvas();
  }

  function mouseUp(e) {
    dragTL = dragTR = dragBL = dragBR = false;
    dragWholeRect = false;
  }

  function cursorIn(e) {
    const pos = getMousePos(canvas, e);
    mouseX = pos.x;
    mouseY = pos.y;
    // 0. inside movable rectangle
    if (checkInRect(mouseX, mouseY, rect)) {
      canvas.style.cursor = "move";
    }
    // 1. top left
    else if (
      checkCloseEnough(mouseX, rect.left) &&
      checkCloseEnough(mouseY, rect.top)
    ) {
      canvas.style.cursor = "nw-resize";
    }
    // 2. top right
    else if (
      checkCloseEnough(mouseX, rect.left + rect.width) &&
      checkCloseEnough(mouseY, rect.top)
    ) {
      canvas.style.cursor = "ne-resize";
    }
    // 3. bottom left
    else if (
      checkCloseEnough(mouseX, rect.left) &&
      checkCloseEnough(mouseY, rect.top + rect.height)
    ) {
      canvas.style.cursor = "ne-resize";
    }
    // 4. bottom right
    else if (
      checkCloseEnough(mouseX, rect.left + rect.width) &&
      checkCloseEnough(mouseY, rect.top + rect.height)
    ) {
      canvas.style.cursor = "nw-resize";
    }
    // (5.) none of them
    else {
      // handle not resizing
      canvas.style.cursor = "auto";
    }
  }

  function init() {
    canvas.addEventListener("mousedown", mouseDown, false);
    canvas.addEventListener("mouseup", mouseUp, false);
    canvas.addEventListener("mousemove", mouseMove, false);
    canvas.addEventListener("touchstart", mouseDown);
    canvas.addEventListener("touchmove", mouseMove);
    canvas.addEventListener("touchend", mouseUp);
    // document
    document.addEventListener("mouseup", mouseUp, false);
    document.addEventListener("touchend", mouseUp);
    initRect();
    drawRectInCanvas();
  }

  function getResult(): Promise<{
    width: number;
    height: number;
    left: number;
    top: number;
    base64: string;
  }> {
    return new Promise((resolve) => {
      const width = Math.ceil((imageWidth * rect.width) / canvas.width);
      const height = Math.ceil((imageHeight * rect.height) / canvas.height);
      const left = Math.ceil((imageWidth * rect.left) / canvas.width);
      const top = Math.ceil((imageHeight * rect.top) / canvas.height);
      if (width > options.maxWidth) {
        const newHeight = (options.maxWidth * height) / width;
        const image2 = new Image();
        image2.onload = () => {
          const base64 = zoomImage(image2, options.maxWidth, newHeight);
          resolve({
            width: options.maxWidth,
            height: newHeight,
            left,
            top,
            base64,
          });
        };
        image2.src = cutImage(image, left, top, width, height);
      } else if (height > options.maxHeight) {
        const newWidth = (options.maxHeight * width) / height;
        const image2 = new Image();
        image2.onload = () => {
          const base64 = zoomImage(image2, newWidth, options.maxHeight);
          resolve({
            width: newWidth,
            height: options.maxHeight,
            left,
            top,
            base64,
          });
        };
        image2.src = cutImage(image, left, top, width, height);
      } else {
        const base64 = cutImage(image, left, top, width, height);
        resolve({ width, height, left, top, base64 });
      }
    });
  }

  init();

  return {
    unmount: () => {
      document.removeEventListener("mouseup", mouseUp, false);
      document.removeEventListener("touchend", mouseUp);
    },
    value: () => {
      return getResult();
    },
  };
};
