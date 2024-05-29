<script lang="ts" setup>
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";
import { computed } from "vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  options: { value: string; label: string }[];
  prompt?: string | false;
};
const props = defineProps<Props>();

const { field, noLabel = false, options, prompt = false } = props;

const update = (e) => {
  field.update(e.target.value);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};

const optionsList = computed(() => {
  return prompt ? [{ value: "", label: prompt }, ...options] : options;
});
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <select
        :id="id"
        class="form-control"
        :class="{ 'is-invalid': field.hasErrors }"
        v-bind="$attrs"
        @input="update"
        @click="touch"
        @blur="blur"
      >
        <option
          v-for="item in optionsList"
          :key="item.value"
          :value="item.value"
          :selected="item.value === field.value"
        >
          {{ item.label }}
        </option>
      </select>
    </template>
  </FieldWrapper>
</template>
