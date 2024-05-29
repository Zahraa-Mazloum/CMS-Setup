<script lang="ts" setup>
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";
import { ref, watch } from "vue";
import Icon from '@/components/Icon.vue';
import { mdiClose } from "@mdi/js";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

export type Item = {
  value: any;
  label: string;
  deletable?: boolean;
};

type Props = {
  field: Field;
  noLabel?: boolean;
  options: Array<Item | "delimiter">;
  inline?: boolean;
};
const { field, noLabel = false, options, inline = false } = defineProps<Props>();

const items = ref<any>(options);

watch(
  () => options,
  (newList: any) => {
    items.value = newList;
  },
  {
    deep: true,
  }
);

const emit = defineEmits(["change", "remove"]);

const update = (e) => {
  let value = field.value;
  if (!Array.isArray(value)) {
    value = [];
  }
  if (!e.target.checked) {
    const index = value.findIndex(
      (str: string) => `${str}` === `${e.target.value}`
    );
    if (index >= 0) {
      value.splice(index, 1);
    }
  } else {
    value = [...new Set([...value, e.target.value])];
  }
  field.update(value);
  emit("change", value);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};

const remove = (item: Item) => {
  if (item.deletable) {
    const index = items.value.findIndex(
      (row: any) => `${row.value}` === `${item.value}`
    );
    if (index >= 0) {
      items.value.splice(index, 1);
      emit("remove", item);
    }
  }
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div class="options">
        <template v-for="(item, i) in items" :key="i">
          <template v-if="item === 'delimiter'">
            <div :class="{ delimiter: true, inline }"></div>
          </template>
          <template v-else>
            <div class="form-check" :class="{ 'form-check-inline': inline }">
              <input
                class="form-check-input"
                type="checkbox"
                :name="`${field.name}[${i}]`"
                :id="`${id}-${i}`"
                :value="item.value"
                v-bind="$attrs"
                @input="update"
                @focus="touch"
                @blur="blur"
                :checked="field?.value?.includes(item.value) ?? false"
              />
              <label
                class="form-check-label position-relative"
                :for="`${id}-${i}`"
              >
                <div
                  v-html="item.label"
                  :class="{ 'ps-3': item.deletable }"
                ></div>
                <template v-if="item.deletable">
                  <Icon :path="mdiClose" color="danger" class="position-absolute top-0 start-0" />
                </template>
              </label>
            </div>
          </template>
        </template>
      </div>
    </template>
  </FieldWrapper>
</template>
<style scoped lang="scss">
@import "@scss/config/colors";

.delimiter {
    height: 21px;
    position: relative;
    min-height: 1.5rem;
    padding-left: 1.5em;
    margin-bottom: .125rem;
    padding-left: 1.5em;
    margin-bottom: .125rem;

    &:before {
        content: "";
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 90%;
        height: 1px;
        background: $gray-3;
        transform: translateX(-50%) translateY(-50%);
    }

    &.inline {
        height: auto;
        min-height: 24px;
        width: 21px;
        display: inline-block;
        margin-right: 1rem;
        top: 7px;

        &:before {
            width: 1px;
            height: 90%;
        }
    }
}
</style>
