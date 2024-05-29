<script setup lang="ts">
import { Field } from "../lib/field";
import { computed } from "vue";
import Icon from "@/components/Icon.vue";
import * as icons from "@mdi/js";
import { GroupSlots } from "./base/FieldWrapper.vue";
import { fields } from "../lib/fields";

type Props = {
  type: string;
  field: Field;
  attributes?: any;
  props?: any;
  icon?: false | string;
};

const {
  type,
  field,
  attributes = {},
  props = {},
  icon = false,
} = defineProps<Props>();

type GroupAttrsSlots = Omit<GroupSlots, "before">;

defineSlots<GroupAttrsSlots>();

const data = computed(() => ({ ...(attributes ?? {}), ...(props ?? {}) }));
</script>
<template>
  <component v-if="fields[type]" :is="fields[type]" :field="field" v-bind="data">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template v-if="icon && Object.hasOwn(icons, icon)" #before>
      <Icon :path="icons[icon]" />
    </template>
  </component>
</template>
