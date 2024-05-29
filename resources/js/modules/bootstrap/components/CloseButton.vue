<script setup lang="ts">
defineOptions({
  inheritAttrs: false,
});
</script>
<template>
  <button
    type="button"
    class="btn-close"
    aria-label="Close"
    v-bind="$attrs"
  ></button>
</template>
