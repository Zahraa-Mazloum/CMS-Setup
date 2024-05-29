<script setup lang="ts">
import { computed } from "vue";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  type?: "items" | "orders" | "actions";
  flush?: boolean;
  horizontal?: boolean;
};
const { type = "items", flush = false, horizontal = false } = defineProps<Props>();

const tag = computed(() =>
  type === "actions" ? "div" : type === "orders" ? "ol" : "ul"
);
</script>
<template>
  <component
    :is="tag"
    class="list-group"
    :class="{
      'list-group-numbered': tag === 'ol',
      'list-group-flush': flush,
      'list-group-horizontal': horizontal,
    }"
    v-bind="$attrs"
  >
    <slot></slot>
  </component>
</template>
