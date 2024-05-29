<script setup lang="ts">
import { ButtonGroupDirection, ButtonSize } from "../types";

type Props = {
  size?: ButtonSize;
  direction?: ButtonGroupDirection
};

const { size = null, direction = "horizontal" } = defineProps<Props>();
</script>
<template>
  <div
    class="btn-group"
    :class="{ ...(size ? { [`btn-group-${size}`]: true } : {}), [`btn-group-${direction}`] : true }"
    role="group"
    v-bind="$attrs"
  >
    <slot></slot>
  </div>
</template>
