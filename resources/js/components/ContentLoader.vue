<script setup lang="ts">
import { ref } from "vue";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  title?: string;
};

const { title = "Content" } = defineProps<Props>();

const loading = ref<boolean>(false);

const start = () => {
  loading.value = true;
};

const stop = () => {
  loading.value = false;
};

defineExpose({ start, stop });
</script>
<template>
  <template v-if="loading">
    <div class="d-flex flex-row gap-4 align-items-center" v-bind="$attrs">
      <div class="spinner-border text-secondary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <div class="text-muted">{{ title }} Loading...</div>
    </div>
  </template>
  <template v-else>
    <slot></slot>
  </template>
</template>
