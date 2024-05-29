<script setup lang="ts">
import type { SpinnerColor, SpinnerType } from "../types";

type Props = {
  color?: SpinnerColor;
  type?: SpinnerType;
  small?: boolean;
};

const {
  color = "primary",
  type = "border",
  small = false,
} = defineProps<Props>();
</script>
<template>
  <span
    :class="{
      [`spinner-${type}`]: true,
      [`text-${color}`]: true,
      ...(small ? { [`spinner-${type}-sm`]: true } : {}),
    }"
    role="status"
    v-bind="$attrs"
  >
    <span class="visually-hidden">Loading...</span>
  </span>
</template>
