<script lang="ts" setup>
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  placeholder?: string;
};
const props = defineProps<Props>();

const { field, noLabel = false, placeholder = props.field.label } = props;

const update = (e) => {
  field.update(e.target.value);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <textarea
        :id="id"
        class="form-control"
        :class="{ 'is-invalid': field.hasErrors }"
        v-bind="$attrs"
        @input="update"
        @focus="touch"
        @blur="blur"
        autocomplete="off"
        spellcheck="false"
        style="resize: none"
        :placeholder="placeholder"
        >{{ field.value }}</textarea
      >
    </template>
  </FieldWrapper>
</template>
