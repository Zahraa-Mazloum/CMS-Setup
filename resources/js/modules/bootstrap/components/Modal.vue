<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { ModalSize, ModalFullscreen } from "../types";
import CloseButton from "./CloseButton.vue";
import Modal from "bootstrap/js/src/modal";
import Icon from "@/components/Icon.vue";
import { mdiClose } from "@mdi/js";
import Button from "./Button.vue";

defineOptions({
  inheritAttrs: false,
});

export type Slots = {
  default?: any;
  title?: any;
  header?: any;
  buttons?: any;
};
defineSlots<Slots>();

export type Expose = {
  open: () => void;
  close: () => void;
  dispose: () => void;
  update: () => void;
};

export type Props = {
  title?: null | string;
  center?: boolean;
  scrollable?: boolean;
  size?: null | ModalSize;
  fullscreen?: boolean | ModalFullscreen;
  backdrop?: boolean | "static";
  focus?: boolean;
  keyboard?: boolean;
  noHeader?: boolean;
  noFooter?: boolean;
  noClose?: boolean;
  controller?: any;
  closeText?: string;
  tag?: string;
  padding?: string;
  noOverfow?: boolean;
};
const {
  title = null,
  center = false,
  scrollable = false,
  size = null,
  fullscreen = false,
  backdrop = "static",
  focus = false,
  keyboard = false,
  noHeader = false,
  noFooter = false,
  noClose = false,
  controller = null,
  closeText = "Close",
  tag = "div",
  padding = "1rem",
  noOverfow = false,
} = defineProps<Props>();

const modalRef = ref<any>(null);
const modal = ref<any>(null);

type Emits = {
  close: any;
  closed: any;
  prevented: any;
  open: any;
  opened: any;
  dismiss: any;
  mounted: any;
  unmounted: any;
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

const onPrevented = (e) => {
  emit("prevented", e);
};

const onOpen = (e) => {
  emit("open", e);
};

const onOpened = (e) => {
  emit("opened", e);
};

const dismiss = (e) => {
  emit("dismiss", e);
};

const create = () => {
  modal.value = new Modal(modalRef.value, {
    backdrop,
    focus,
    keyboard,
  });
  modalRef.value?.addEventListener?.("hide.bs.modal", onClose);
  modalRef.value?.addEventListener?.("hidden.bs.modal", onClosed);
  modalRef.value?.addEventListener?.("hidePrevented.bs.modal", onPrevented);
  modalRef.value?.addEventListener?.("show.bs.modal", onOpen);
  modalRef.value?.addEventListener?.("shown.bs.modal", onOpened);
};

const open = () => {
  modal.value?.show?.();
};

const close = () => {
  modal.value?.hide?.();
};

const dispose = () => {
  modalRef.value?.removeEventListener?.("hide.bs.modal", onClose);
  modalRef.value?.removeEventListener?.("hidden.bs.modal", onClosed);
  modalRef.value?.removeEventListener?.("hidePrevented.bs.modal", onPrevented);
  modalRef.value?.removeEventListener?.("show.bs.modal", onOpen);
  modalRef.value?.removeEventListener?.("shown.bs.modal", onOpened);
  modalRef.value?.dispose?.();
};

const update = () => {
  modal.value?.handleUpdate?.();
};

defineExpose<Expose>({ open, close, dispose, update });

onMounted(() => {
  create();
  emit('mounted')
});

onUnmounted(() => {
  dispose();
  emit('unmounted')
});
</script>
<template>
  <Teleport to="body">
    <component
      :is="tag"
      ref="modalRef"
      class="modal fade"
      tabindex="-1"
      v-bind="$attrs"
    >
      <div
        class="modal-dialog"
        :class="{
          'modal-dialog-centered': center,
          'modal-dialog-scrollable': scrollable,
          ...(size ? { [`modal-${size}`]: true } : {}),
          ...(fullscreen
            ? {
                [`modal-fullscreen${
                  typeof fullscreen === 'string' ? `-${fullscreen}-down` : ''
                }`]: true,
              }
            : {}),
        }"
      >
        <div class="modal-content">
          <div v-if="!noHeader" class="modal-header">
            <slot name="title">
              <h5 class="modal-title" v-html="title"></h5>
            </slot>
            <slot name="header"></slot>
            <template v-if="!noClose">
              <CloseButton
                data-bs-dismiss="modal"
                aria-label="Close"
                @click="dismiss"
                class="align-self-start"
              />
            </template>
          </div>
          <div
            v-if="$slots.default"
            class="modal-body"
            :style="{ '--bs-modal-padding': padding, overflow: noOverfow ? 'hidden' : 'auto' }"
          >
            <slot></slot>
          </div>
          <div v-if="!noFooter" class="modal-footer">
            <div class="d-flex flex-row gap-3">
              <template v-if="!noClose">
                <Button color="dark" data-bs-dismiss="modal" @click="dismiss">
                  <Icon :path="mdiClose" />{{ closeText }}
                </Button>
              </template>
              <slot name="buttons"></slot>
            </div>
          </div>
        </div>
      </div>
    </component>
  </Teleport>
</template>
