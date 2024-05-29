<script setup lang="ts">
import { ListGroupColor } from "../types";
import { getCurrentInstance, computed } from 'vue';

defineOptions({
  inheritAttrs: false,
});

type Props = {
  active?: boolean;
  disabled?: boolean;
  color?: ListGroupColor;
};
const {
  active = false,
  disabled = false,
  color = "light",
} = defineProps<Props>();

const instance = getCurrentInstance();
const parentTag = computed(
  () => instance?.parent?.props?.type?.toString?.()?.toLowerCase?.() ?? null
);

const clickable = computed(() => parentTag.value === 'actions')

</script>
<template>
  <component
    :is="clickable ? 'a' : 'li'"
    :href="clickable ? '#' : null"
    class="list-group-item"
    :class="{
      'list-group-item-action': clickable,
      [`list-group-item-${color}`]: true,
      active,
      disabled,
    }"
    :aria-current="active ? 'true' : undefined"
    :aria-disabled="disabled ? 'true' : undefined"
    @click.prevent
    v-bind="$attrs"
  >
    <slot></slot>
  </component>
</template>
