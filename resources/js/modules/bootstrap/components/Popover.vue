<script setup lang="ts">
import { onMounted, ref, nextTick, onUnmounted } from "vue";
import { Popover } from "bootstrap";
import { createSlotRef } from "@/hooks/slot-ref";
import Button from "@/modules/bootstrap/components/Button.vue";
import { autoId } from "@/lib/helper/strings";

defineOptions({
  inheritAttrs: false,
});

type OffsetsFunction = ({
  popper,
  reference,
  placement,
}: any) => [number, number];

type Props = {
  title?: string;
  trigger?: "hover" | "focus" | "click" | "manual";
  animation?: boolean;
  delay?: number | any;
  customClass?: string;
  container?: any;
  disabled?: boolean;
  placement?: "auto" | "top" | "bottom" | "left" | "right";
  offset?: string | OffsetsFunction | number[];
  width?: string;
  controller?: any;
  padding?: string;
};
const {
  title = "",
  animation = false,
  delay = 0,
  customClass = "",
  container = false,
  trigger = "hover",
  disabled = false,
  placement = "auto",
  offset = [0, 8],
  width = "auto",
  controller = null,
  padding = "1rem",
} = defineProps<Props>();

const popover = ref<any>(null);

const [target, setTarget] = createSlotRef();
const [content, setContent] = createSlotRef();
const [template, setTemplate] = createSlotRef();

type Slots = {
  default: (props: { element: (el: any) => void }) => void;
  content: (props: { element: (el: any) => void }) => void;
  template: (props: { element: (el: any) => void }) => void;
};

defineSlots<Slots>();

const toX = ref<any>(null);

const key = autoId("popover");

type Emits = {
  open: [e: any];
  opened: [e: any];
  close: [e: any];
  closed: [e: any];
  inserted: [e: any];
};
const emit = defineEmits<Emits>();

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
  emit("opened", e);
  const wrapper: any = document.querySelector(`.${key}`);
  if (wrapper) {
    wrapper.style.setProperty("--bs-popover-max-width", width);
    wrapper.style.setProperty("width", width);
    const body: any = wrapper.querySelector(".popover-body");
    if (body) {
      body.style.setProperty("--bs-popover-body-padding-x", padding);
      body.style.setProperty("--bs-popover-body-padding-y", padding);
    }
  }
};

const onInserted = (e) => {
  emit("inserted", e);
};

const createPopover = () => {
  popover.value = new Popover(target.value, {
    title: title,
    content: content.value,
    trigger,
    animation,
    delay,
    customClass,
    container,
    placement,
    template: template.value.outerHTML,
    offset,
    html: true,
  });
  nextTick(() => {
    target.value?.addEventListener("show.bs.popover", onOpen);
    target.value?.addEventListener("shown.bs.popover", onOpened);
    target.value?.addEventListener("hide.bs.popover", onClose);
    target.value?.addEventListener("hidden.bs.popover", onClosed);
    target.value?.addEventListener("inserted.bs.popover", onInserted);
    toX.value?.remove();
  });
};

const toggle = () => {
  popover.value.toggle();
};
const open = () => {
  popover.value.show();
};
const close = () => {
  popover.value.hide();
};

const dispose = () => {
  target.value?.removeEventListener?.("hide.bs.popover", onClose);
  target.value?.removeEventListener?.("hidden.bs.popover", onClosed);
  target.value?.removeEventListener?.("show.bs.popover", onOpen);
  target.value?.removeEventListener?.("shown.bs.popover", onOpened);
  target.value?.addEventListener("inserted.bs.popover", onInserted);
  popover.value?.dispose?.();
};

const disable = () => {
  popover.value.disable();
};

const enable = () => {
  popover.value.enable();
};

const updateContent = (header = null, body = null) => {
  const conf: any = {};
  if (header) {
    conf[".popover-header"] = header;
  }
  if (body) {
    conf[".popover-body"] = body;
  }
  popover.value.setContent();
};

const toggleEnabled = () => {
  popover.value.toggleEnabled();
};

const update = () => {
  popover.value.update();
};

defineExpose({
  open,
  close,
  toggle,
  dispose,
  disable,
  enable,
  updateContent,
  toggleEnabled,
  update,
});

onMounted(() => {
  createPopover();
  setTimeout(() => {
    const root = content.value?.parentElement?.parentElement ?? null;
    if (root) {
      root.style.width = "100%";
      root.style.maxWidth = width;
    }
  }, 500);
});

onUnmounted(() => {
  dispose();
});
</script>
<template>
  <slot :element="setTarget">
    <Button :ref="setTarget">{{ title }}</Button>
  </slot>
  <Teleport to="body">
    <div style="display: none" ref="toX">
      <slot name="content" :element="setContent"></slot>
      <slot name="template" :element="setTemplate">
        <div
          :class="{ popover: true, [`popover-${width}`]: true, [key]: true }"
          role="tooltip"
          :ref="setTemplate"
          :style="{ '--bs-popover-max-width': 'auto', width }"
        >
          <div class="popover-arrow"></div>
          <h3 class="popover-header"></h3>
          <div class="popover-body"></div>
        </div>
      </slot>
    </div>
  </Teleport>
</template>
