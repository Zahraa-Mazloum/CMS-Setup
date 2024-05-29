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
  options?: Array<string>;
};
const props = defineProps<Props>();

const { field, noLabel = false, options = [] } = props;

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
      <input
        :id="id"
        :class="{ 'is-invalid': field.hasErrors }"
        :value="field.value"
        type="range"
        v-bind="$attrs"
        @input="update"
        @focus="touch"
        @blur="blur"
        autocomplete="off"
        spellcheck="false"
        :list="`list-of-${id}`"
      />
      <datalist v-if="options.length > 0" :id="`list-of-${id}`">
        <option
          v-for="value in options"
          :key="value"
          :value="value"
          :label="`${value}`"
        ></option>
      </datalist>
    </template>
  </FieldWrapper>
</template>
