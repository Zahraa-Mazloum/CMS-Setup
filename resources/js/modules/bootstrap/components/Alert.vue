<script setup lang="ts">
import { ref, onMounted, onUnmounted, nextTick } from "vue";
import { AlertColor } from "../types";
import Alert from "bootstrap/js/src/alert";
import CloseButton from "./CloseButton.vue";

type Props = {
  color?: AlertColor;
  dismiss?: boolean;
};

const { color = "primary", dismiss = false } = defineProps<Props>();

const alert = ref<any>(null);
const alertRef = ref<any>(null);

type Emits = {
  close: [event: any];
  closed: [event: any];
};

const emit = defineEmits<Emits>();

const create = () => {
  alert.value = new Alert(alertRef.value);
};

const onClose = (event: any) => {
  emit("close", event);
};
const onClosed = (event: any) => {
  emit("closed", event);
};

const close = () => {
  alert.value?.close();
};

onMounted(() => {
  create();
  nextTick(() => {
    alertRef.value.addEventListener("close.bs.alert", onClose);
    alertRef.value.addEventListener("closed.bs.alert", onClosed);
  });
});

onUnmounted(() => {
  close();
  setTimeout(() => {
    alert.value?.dispose();
    alertRef.value.removeEventListener("close.bs.alert", onClose);
    alertRef.value.removeEventListener("closed.bs.alert", onClosed);
  }, 300);
});

defineExpose({close})
</script>
<template>
  <div
    ref="alertRef"
    :class="{ alert: true, [`alert-${color}`]: true, fade: true, show: true, 'alert-dismissible' : dismiss }"
    role="alert"
  >
    <slot></slot>
    <template v-if="dismiss">
      <CloseButton @click.prevent.stop="close" />
    </template>
  </div>
</template>
