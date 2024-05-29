<script setup lang="ts">
import { translate } from "@/lib/metadata";
import Icon from "@/components/Icon.vue";
import { mdiClose, mdiSearchWeb } from "@mdi/js";
import { ref } from "vue";

type Props = {
  modelValue: string;
};

const { modelValue } = defineProps<Props>();

const emit = defineEmits(["update:modelValue", "clear", "search"]);

const changeValue = (event) => {
  emit("update:modelValue", event.target.value.trim());
};
const clear = () => {
  emit("clear");
};
const search = () => {
  emit("search");
};
</script>
<template>
  <div class="input-group search-input-group">
    <template v-if="$slots['start-search']">
      <slot name="start-search"> </slot>
    </template>
    <button
      v-if="modelValue !== ''"
      type="button"
      class="btn btn-primary"
      @click="clear"
    >
      <Icon :path="mdiClose" />
    </button>
    <template v-if="$slots['before-search']">
      <slot name="before-search"> </slot>
    </template>
    <input
      type="text"
      class="form-control"
      :placeholder="translate('Search...')"
      :value="modelValue"
      @input="changeValue($event)"
      @keydown.stop.enter="search"
      @keydown.stop.esc="clear"
    />
    <template v-if="$slots['after-search']">
      <slot name="after-search"> </slot>
    </template>
    <button type="button" class="btn btn-primary" @click.prevent="search">
      <Icon :path="mdiSearchWeb" />
    </button>
    <template v-if="$slots['end-search']">
      <slot name="end-search"> </slot>
    </template>
  </div>
</template>
