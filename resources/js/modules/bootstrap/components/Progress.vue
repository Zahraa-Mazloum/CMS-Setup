<script setup lang="ts">
import { ref, watch, computed, nextTick } from "vue";
import { ProgressColor } from "../types";

type Props = {
  value: number;
  min?: number;
  max?: number;
  height?: string;
  color?: ProgressColor;
  striped?: boolean;
  animated?: boolean;
  stacked?: boolean;
};

const {
  value,
  min = 0,
  max = 100,
  height = "1rem",
  color = "primary",
  striped = false,
  animated = false,
  stacked = false,
} = defineProps<Props>();

const progress = ref<number>(value);

watch(
  () => value,
  (newValue: number) => {
    progress.value = newValue;
  }
);

const percentage = computed(() => {
  return (progress.value * 100) / max;
});

const emit = defineEmits(["done"]);

watch(
  () => percentage.value,
  () => {
    if (percentage.value >= 100) {
      setTimeout(() => {
        emit("done");
      }, 500);
    }
  }
);
</script>
<template>
  <div
    class="progress"
    role="progressbar"
    aria-label="Basic example"
    :aria-valuenow="percentage"
    :aria-valuemin="min"
    :aria-valuemax="max"
    :style="{ height, width: stacked ? `${percentage}%` : '100%' }"
  >
    <div
      class="progress-bar overflow-visible"
      :class="{
        'progress-bar-striped': striped,
        'progress-bar-animated': animated,
        [`bg-${color}`]: true,
      }"
      :style="{ width: stacked ? '100%' : `${percentage}%` }"
    >
      <slot
        :percentage="percentage"
        :progress="progress"
        :min="min"
        :max="max"
      ></slot>
    </div>
  </div>
</template>
