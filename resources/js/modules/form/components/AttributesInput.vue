<script lang="ts" setup>
import Attributes from "@/components/Attributes.vue";
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";
import { watch } from "vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  fixed?: boolean;
  readOnlyName?: boolean;
  nameAsValue?: boolean;
};
const {
  field,
  fixed = false,
  readOnlyName = false,
  nameAsValue = false,
} = defineProps<Props>();

watch(
  () => field.value,
  (value) => {
    field.update(value);
  }
);
</script>
<template>
  <FieldWrapper :field="field" no-label>
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <Attributes
      v-model="field.value"
      :fixed="fixed"
      :read-only-name="readOnlyName"
      :name-as-value="nameAsValue"
    />
  </FieldWrapper>
</template>
