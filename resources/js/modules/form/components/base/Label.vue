<script lang="ts" setup>
import { Field } from "../../lib/field";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  field: Field;
};

const { field } = defineProps<Props>();
</script>
<template>
  <label class="form-label" v-bind="$attrs" v-html="field.htmlLabel"></label>
</template>
<style scoped lang="scss"></style>
