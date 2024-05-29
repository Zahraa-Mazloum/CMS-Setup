<script setup lang="ts">
defineOptions({
  inheritAttrs: false,
});

type Slots = {
  default: any;
};
defineSlots<Slots>();

type Props = {
  tag?: string | false;
};
const { tag = false } = defineProps<Props>();
</script>
<template>
  <template v-if="tag">
    <component :is="tag" v-bind="$attrs">
      <slot></slot>
    </component>
  </template>
  <template v-else>
    <slot></slot>
  </template>
</template>
