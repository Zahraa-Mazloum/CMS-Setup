<script lang="ts" setup>
import { getCurrentInstance, onMounted } from 'vue';
import { Field } from "../../lib/field";
import Label from "./Label.vue";
import Error from "./Error.vue";
import { autoId } from "@/lib/helper/strings";
import Hint from './Hint.vue';

defineOptions({
  inheritAttrs: false,
});

type Slots = {
  default: (props: { id: string }) => any;
  label: any;
};

export type GroupSlots = {
  before: (props: { field: Field }) => any;
  after: (props: { field: Field }) => any;
  buttonBefore: (props: { field: Field }) => any;
  buttonAfter: (props: { field: Field }) => any;
};

defineSlots<Slots & GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
};

const { field, noLabel = false } = defineProps<Props>();

const instance: any = getCurrentInstance();

field?.events.afterValidate.listen(() => {
  instance?.proxy?.$forceUpdate();
});

const id = autoId(`${field.name}`);

onMounted(() => {
  if (field && instance) {
    field.component = instance;
  }
});
</script>
<template>
  <div
    v-if="field"
    class="field-wrapper"
    :class="{ [`field-wrapper-${field.name}`]: true }"
    v-bind="$attrs"
  >
    <slot v-if="field && !noLabel" name="label">
      <Label :field="field" :for="id" />
    </slot>
    <Hint :field="field" />
    <div class="input-group">
      <template v-if="$slots.before">
        <div class="input-group-text">
          <slot name="before" :field="field"></slot>
        </div>
      </template>
      <template v-if="$slots.buttonBefore">
        <slot name="buttonBefore" :field="field"></slot>
      </template>
      <slot :id="id" />
      <template v-if="$slots.after">
        <div class="input-group-text">
          <slot name="after" :field="field"></slot>
        </div>
      </template>
      <template v-if="$slots.buttonAfter">
        <slot name="buttonAfter" :field="field"></slot>
      </template>
    </div>
    <Error v-if="field && field.hasErrors" :field="field" />
  </div>
</template>
