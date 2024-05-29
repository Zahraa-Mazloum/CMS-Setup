<script setup lang="ts">
import Button from "@/modules/bootstrap/components/Button.vue";
import { ref, onMounted, nextTick } from "vue";

defineOptions({
  inheritAttrs: false,
});

type Slots = {
  default: any;
  content(props: { element: (el: any) => void; closed: () => void }): any;
};
defineSlots<Slots>();

type Emits = {
  open: any;
  close: any;
};
const emit = defineEmits<Emits>();

const elementRef = ref<any>(null);
const opened = ref<boolean>(false);

const open = () => {
  opened.value = true;
  nextTick(() => {
    elementRef.value?.open?.();
    nextTick(() => {
      elementRef.value?.update?.();
      nextTick(() => {
        emit("open");
      });
    });
  });
};

const elementRefHandler = (el: any) => {
  elementRef.value = el;
};

const closed = () => {
  opened.value = false;
  emit("close");
};

defineExpose({ open });
</script>
<template>
  <Button @click="open" v-bind="$attrs">
    <slot></slot>
  </Button>
  <template v-if="opened">
    <slot name="content" :element="elementRefHandler" :closed="closed"></slot>
  </template>
</template>
