<script lang="ts" setup>
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";
import { ref } from "vue";

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

const input = ref<any>(null);

const update = (e) => {
  field.update(e.target.value);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};

defineExpose({ input: () => input.value });
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <input
        :id="id"
        ref="input"
        class="form-control"
        :class="{ 'is-invalid': field.hasErrors }"
        :value="field.value"
        v-bind="$attrs"
        @input="update"
        @focus="touch"
        @blur="blur"
        autocomplete="off"
        spellcheck="false"
        :placeholder="placeholder"
      />
    </template>
  </FieldWrapper>
</template>
