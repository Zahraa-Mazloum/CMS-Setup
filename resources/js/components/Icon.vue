<script setup lang="ts">
import { Color } from "@/modules/bootstrap/types";
import { computed, ref, getCurrentInstance, onMounted } from "vue";

defineOptions({
  inheritAttrs: false,
});

export type Props = {
  path: string;
  type?: "mdi" | "simple-icons";
  size?: string | number;
  viewbox?: string;
  flip?: "none" | "horizontal" | "vertical" | "both";
  rotate?: number;
  color?: undefined | "muted" | Color;
};

const {
  path,
  type = "mdi",
  size = 24,
  viewbox = "0 0 24 24",
  flip = "none",
  rotate = 0,
  color = undefined,
} = defineProps<Props>();

const types = {
  mdi: {
    size: 24,
    viewbox: "0 0 24 24",
  },
  "simple-icons": {
    size: 24,
    viewbox: "0 0 24 24",
  },
  default: {
    size: 0,
    viewbox: "0 0 0 0",
  },
};

const defaults = computed(() => types[type] || types.default);
const sizeValue = computed(() => size || defaults.value.size);
const viewboxValue = computed(() => viewbox || defaults.value.viewbox);

const instance = getCurrentInstance();

const svg = ref<any>(null);

const styles = computed(() => ({
  "--sx": ["both", "horizontal"].includes(flip) ? "-1" : "1",
  "--sy": ["both", "vertical"].includes(flip) ? "-1" : "1",
  "--r": isNaN(rotate) ? rotate : rotate + "deg",
  "--color": color ? `var(--bs-${color})` : "currentColor",
  "--size": `${sizeValue.value}px`,
}));

export type Expose = {
  svg: () => string;
};

defineExpose<Expose>({ svg: () => svg.value?.innerHtml });

onMounted(() => {
  const host = instance!.vnode!.el!.parentNode.host;
  if (host && typeof host === "object") {
    host.style.setProperty("--size", `${sizeValue.value}px`);
  }
});
</script>
<template>
  <svg
    :width="sizeValue"
    :height="sizeValue"
    :viewBox="viewboxValue"
    :style="styles"
    :class="{ [`${color ? 'text' : 'default'}-${color ?? 'color'}`]: true }"
    v-bind="$attrs"
  >
    <path :d="path" style="fill: currentColor" />
  </svg>
</template>
<style scoped lang="scss">
svg {
  transform: rotate(var(--r, 0deg)) scale(var(--sx, 1), var(--sy, 1));
  fill: var(--color);
  path {
    fill: currentColor;
  }
}
</style>
