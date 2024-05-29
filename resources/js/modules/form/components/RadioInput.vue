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
  options: { value: string; label: string }[];
  inline?: boolean;
};
const props = defineProps<Props>();

const { field, noLabel = false, options, inline = false } = props;

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
      <div class="options">
        <template v-for="(item, i) in options" :key="i">
          <div class="form-check" :class="{ 'form-check-inline': inline }">
            <input
              class="form-check-input"
              type="radio"
              :name="field.name"
              :id="`${id}-${i}`"
              :value="item.value"
              v-bind="$attrs"
              @input="update"
              @focus="touch"
              @blur="blur"
              :checked="field.value === item.value"
            />
            <label class="form-check-label" :for="`${id}-${i}`">
              {{ item.label }}
            </label>
          </div>
        </template>
      </div>
    </template>
  </FieldWrapper>
</template>
