<script setup lang="ts">
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
   <div v-if="field.hint" v-html="field.hint" class="pb-2 text-muted"></div> 
</template>
