<script setup lang="ts">
import { computed, ref, nextTick } from "vue";
import type {
  ButtonColor,
  ButtonType,
  ButtonSize,
  SpinnerColor,
  SpinnerType,
} from "../types";
import Spinner from "./Spinner.vue";

type Props = {
  color?: ButtonColor;
  outline?: boolean;
  size?: null | ButtonSize;
  type?: ButtonType;
  disabled?: boolean;
  spinnerColor?: SpinnerColor;
  spinnerType?: SpinnerType;
  spinnerSmall?: boolean;
  direction?: "horizontal" | "vertical";
};

const {
  color = "primary",
  outline = false,
  size = null,
  type = "button",
  disabled = false,
  spinnerColor = "primary",
  spinnerType = "border",
  spinnerSmall = false,
  direction = "horizontal",
} = defineProps<Props>();

const classes = computed(() => {
  const cls: any = { btn: true };
  if (outline) {
    cls[`btn-outline-${color}`] = true;
  } else {
    cls[`btn-${color}`] = true;
  }
  if (size) {
    cls[`btn-${size}`] = true;
  }
  return cls;
});

const isDisabled = ref<boolean>(disabled);
const loading = ref<boolean>(false);
const btn = ref<any>(null);

const enable = () => {
  isDisabled.value = false;
};

const disable = () => {
  isDisabled.value = true;
};

const start = () => {
  loading.value = true;
  nextTick(() => {
    disable();
  });
};

const stop = () => {
  loading.value = false;
  nextTick(() => {
    enable();
  });
};

defineExpose({ enable, disable, start, stop, spinning: () => loading.value, element: () => btn.value });
</script>
<template>
  <button :type="type" :class="classes" :disabled="isDisabled" v-bind="$attrs" ref="btn">
    <template v-if="loading">
      <Spinner
        :color="spinnerColor"
        :type="spinnerType"
        :small="spinnerSmall"
      />
    </template>
    <template v-else>
      <slot></slot>
    </template>
  </button>
</template>
<style scoped lang="scss">
button {
  display: flex;
  flex-direction: v-bind(direction);
  gap: 10px;
  white-space: nowrap;
  justify-content: center;
  align-items: center;
}
</style>
