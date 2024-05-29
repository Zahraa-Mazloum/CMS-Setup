<script lang="ts" setup>
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  trueValue?: string | number;
  falseValue?: string | number;
};
const props = defineProps<Props>();

const { field, trueValue = 1, falseValue = 0 } = props;

const update = (e) => {
  field.update(e.target.checked ? trueValue : falseValue);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};
</script>
<template>
  <FieldWrapper :field="field" no-label>
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div class="form-check form-switch">
        <input
          :id="id"
          class="form-check-input"
          type="checkbox"
          role="switch"
          :class="{ 'is-invalid': field.hasErrors }"
          :value="trueValue"
          v-bind="$attrs"
          @input="update"
          @focus="touch"
          @blur="blur"
          :checked="field.value === trueValue"
        />
        <label class="form-check-label" :for="id">
          <slot>{{ field.label }}</slot>
        </label>
      </div>
    </template>
  </FieldWrapper>
</template>
