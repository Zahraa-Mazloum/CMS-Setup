<script lang="ts" setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
};
const props = defineProps<Props>();

const { field, noLabel = false } = props;

const initValue = field.value;

const input = ref<any>(null);

const updateHtml = (html) => {
  const regex = /<div>(.*?)<\/div>/gm;

  html = html.replace(regex, function(text, subText){
    return `\n${subText}`;
  });

  field.update(html);
}

const update = (e) => {
  updateHtml(e.target.innerHTML)
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};

let observer: any = null;

onMounted(() => {
  observer = new MutationObserver((mutationList, observer) => {
    updateHtml(input.value.innerHTML)
  });

  observer.observe(input.value, {
    subtree: true,
    childList: true,
    //attributes: true,
  });
});

onUnmounted(() => observer && observer.disconnect());
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div
        :id="id"
        contenteditable="true"
        class="form-control"
        :class="{ 'is-invalid': field.hasErrors }"
        v-bind="$attrs"
        @input="update"
        @focus="touch"
        @blur="blur"
        autocomplete="off"
        spellcheck="false"
        v-html="initValue"
        ref="input"
      ></div>
    </template>
  </FieldWrapper>
</template>
<style scoped lang="scss">
.form-control {
  position: relative;
  -moz-appearance: textfield-multiline;
  -webkit-appearance: textarea;
  resize: none;
  white-space: pre-wrap;
  &:before {
    content: attr(placeholder);
    display: block; /* For Firefox */
  }
  &:focus {
    outline: none;
    &:before {
      display: none;
      content: "";
    }
  }
}
</style>
