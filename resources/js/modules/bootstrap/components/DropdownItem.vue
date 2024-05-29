<script setup lang="ts">
import { getCurrentInstance, computed } from "vue";

defineOptions({
  inheritAttrs: false,
});

const instance = getCurrentInstance();

const type = computed(
  () =>
    instance?.parent?.subTree?.children?.[2]?.type
      ?.toString?.()
      ?.toLowerCase?.() ?? null
);
</script>
<template>
  <template v-if="type === 'ul'">
    <li>
      <a class="dropdown-item" href="#" v-bind="$attrs" @click.prevent>
        <slot></slot>
      </a>
    </li>
  </template>
  <template v-if="type === 'div'">
    <a class="dropdown-item" href="#" v-bind="$attrs" @click.prevent>
      <slot></slot>
    </a>
  </template>
</template>
