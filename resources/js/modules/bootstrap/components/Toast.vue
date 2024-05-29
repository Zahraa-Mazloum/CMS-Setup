<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted, getCurrentInstance } from "vue";
import Toast from "bootstrap/js/src/toast";
import { ToastPosition, ToastColor } from "../types";
import CloseButton from "./CloseButton.vue";
import Progress from "./Progress.vue";

const instance = getCurrentInstance();

defineOptions({
  inheritAttrs: false,
});

export type Slots = {
  default?: any;
  title?: any;
  titleStart?: any;
  titleEnd?: any;
};
const slots = defineSlots<Slots>();

export type Expose = {
  open: () => void;
  close: () => void;
  isShown: () => boolean;
  dispose: () => void;
};

export type Props = {
  title?: false | string;
  position?: ToastPosition;
  color?: null | ToastColor;
  animated?: boolean;
  autohide?: boolean;
  delay?: number;
  controller?: any;
};

const {
  title = false,
  position = "bottom-end",
  color = null,
  animated = false,
  autohide = false,
  delay = 5000,
  controller = null,
} = defineProps<Props>();

const toastRef = ref<any>(null);
const toast = ref<any>(null);
const progress = ref<number>(0);

const containerId = computed(() => `container-${position}`);

type Emits = {
  init: any;
  create: any;
  close: any;
  closed: any;
  open: any;
  opened: any;
};
const emit = defineEmits<Emits>();

emit("init", instance);

const onClose = (e) => {
  emit("close", e);
};

const onClosed = (e) => {
  emit("closed", e);
  if (controller) {
    controller.removeController();
  }
};

const onOpen = (e) => {
  emit("open", e);
};

const onOpened = (e) => {
  playProgress();
  emit("opened", e);
};

const createAriaPolite = () => {
  let container = document.querySelector(`#aria-polite`);
  if (!container) {
    container = document.createElement("div");
    container.setAttribute("id", "aria-polite");
    container.setAttribute("aria-live", "polite");
    container.setAttribute("aria-atomic", "true");
    container.classList.add("position-relative");
    document.body.appendChild(container);
  }
  return container;
};

const createContainer = () => {
  if (!document.querySelector(`#${containerId.value}`)) {
    const politeContainer = createAriaPolite();
    const parts = position.split("-");
    const middle: any =
      position === "center"
        ? "translate-middle"
        : parts[0] === "center"
        ? "translate-middle-y"
        : parts[1] === "center"
        ? "translate-middle-x"
        : null;
    let y = parts[0];
    if (y === "center") {
      y = "top-50";
    } else {
      y = `${y}-0`;
    }

    let x = parts[1];
    if (!x || x === "center") {
      x = "start-50";
    } else {
      x = `${x}-0`;
    }

    const container = document.createElement("div");
    container.setAttribute("id", containerId.value);
    container.classList.add(
      "toast-container",
      "position-fixed",
      x,
      y,
      middle,
      "p-3"
    );
    politeContainer.appendChild(container);
  }
};

const create = () => {
  toast.value = new Toast(toastRef.value, {
    animation: animated,
    autohide: false,
    delay: 0,
  });
  toastRef.value?.addEventListener?.("hide.bs.toast", onClose);
  toastRef.value?.addEventListener?.("hidden.bs.toast", onClosed);
  toastRef.value?.addEventListener?.("show.bs.toast", onOpen);
  toastRef.value?.addEventListener?.("shown.bs.toast", onOpened);
  emit("create", toast.value, toastRef.value, instance);
};

const open = () => {
  toast.value?.show?.();
};

const close = () => {
  toast.value?.hide?.();
};

const isShown = () => {
  toast.value?.isShown?.();
};

const dispose = () => {
  toastRef.value?.removeEventListener?.("hide.bs.toast", onClose);
  toastRef.value?.removeEventListener?.("hidden.bs.toast", onClosed);
  toastRef.value?.removeEventListener?.("show.bs.toast", onOpen);
  toastRef.value?.removeEventListener?.("shown.bs.toast", onOpened);
};

const playProgress = () => {
  if (autohide && color && delay > 0) {
    const id = setInterval(() => {
      if (progress.value >= delay) {
        clearInterval(id);
      } else {
        progress.value += 50;
      }
    }, 50);
  }
};

createContainer();

defineExpose({ open, close, isShown, dispose });

onMounted(() => {
  create();
});

onUnmounted(() => {
  dispose();
});
</script>
<template>
  <Teleport :to="`#${containerId}`">
    <div
      ref="toastRef"
      class="toast overlow-hidden"
      :class="{
        [`text-bg-${color}`]: color !== null,
        'border-0': color !== null,
      }"
      role="alert"
      aria-live="assertive"
      aria-atomic="true"
      v-bind="$attrs"
    >
      <div v-if="title || $slots.title" class="toast-header">
        <slot name="titleStart"></slot>
        <slot name="title">
          <strong class="me-auto">{{ title }}</strong>
        </slot>
        <slot name="titleEnd"></slot>
        <CloseButton data-bs-dismiss="toast" />
      </div>
      <div v-if="$slots.default" class="toast-body">
        <slot></slot>
      </div>
      <template v-if="autohide && color && delay > 0">
        <Progress
          :value="progress"
          height="3px"
          :min="0"
          :max="delay"
          :color="color"
          striped
          animated
          class="rounded-0 border-top"
          @done="close"
        />
      </template>
    </div>
  </Teleport>
</template>
