<script setup lang="ts">
import Offcanvas, { Emits } from "@/modules/bootstrap/components/Offcanvas.vue";
import ToggleButton from "@/modules/bootstrap/components/ToggleButton.vue";
import { ref, onMounted, getCurrentInstance } from "vue";

defineOptions({
  inheritAttrs: false,
});

type Slots = {
  default: any;
  button: any;
  title: any;
};
defineSlots<Slots>();

const emit = defineEmits<Emits>();

const instance: any = getCurrentInstance();

const theme = ref<any>(null);
const toggleButton = ref<any>(null);
const offCanvas = ref<any>(null);

const setOffCanvas = (el: any) => {
  offCanvas.value = el;
};

const onClose = (e) => {
  emit("close", e);
};

const onClosed = (e) => {
  emit("closed", e);
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

const open = () => {
  toggleButton.value?.open?.();
};

const close = () => {
  offCanvas.value?.close?.();
};

defineExpose({ open, close });

onMounted(() => {
  theme.value = document.documentElement.dataset.bsTheme;
  instance!.vnode!.el!.parentNode.host.open = open;
  instance!.vnode!.el!.parentNode.host.close = close;
  instance!.vnode!.el!.parentNode.host.shadowRoot.appendChild(
    document.querySelector("#main-css")?.cloneNode()
  );
});
</script>
<template>
  <ToggleButton ref="toggleButton">
    <template #default>
      <slot name="button"></slot>
    </template>
    <template #content="{ element, closed }">
      <Offcanvas
        :ref="
          (el) => {
            element(el);
            setOffCanvas(el);
          }
        "
        v-bind="$attrs"
        native
        :class="{
          dark: theme === 'dark',
        }"
        @close="onClose"
        @closed="
          (e) => {
            closed();
            onClosed(e);
          }
        "
        @prevented="onPrevented"
        @open="onOpen"
        @opened="onOpened"
      >
        <template #title>
          <slot name="title"></slot>
        </template>
        <slot></slot>
      </Offcanvas>
    </template>
  </ToggleButton>
</template>
<style lang="scss">
.dark {
  .btn-close {
    filter: var(--bs-btn-close-white-filter);
  }
}
</style>
