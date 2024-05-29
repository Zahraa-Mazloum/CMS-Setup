<script lang="ts" setup>
import SelectFile from "../SelectFile.vue";
import { Field } from "@/modules/form/lib/field";
import FieldWrapper, { GroupSlots } from "@/modules/form/components/base/FieldWrapper.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  section?: string;
  accept?: Array<string> | string;
};
const props = defineProps<Props>();

const { field, noLabel = false, section = "files", accept = "*" } = props;

const update = (image: any) => {
  field.update(image);
};

const touch = (type, data) => {
  field.touch({ target: { value: data } });
};

const blur = (base64) => {
  field.blur({ target: { value: base64 } });
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <SelectFile
        :id="id"
        :accept="accept"
        :section="section"
        :url="field.value ?? null"
        @add="touch"
        @file="update"
        @done="blur"
      >
        <template #add>
          <slot name="add"></slot>
        </template>
      </SelectFile>
    </template>
  </FieldWrapper>
</template>
