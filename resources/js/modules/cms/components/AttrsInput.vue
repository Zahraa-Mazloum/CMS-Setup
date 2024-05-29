<script lang="ts" setup>
import { Attrs } from '@/modules/cms/forms/attrs';
import AttrsInput from '@/modules/form/components/AttrsInput.vue';

defineOptions({
  inheritAttrs: false,
});

type Props = {
  model: Attrs;
};
const { model } = defineProps<Props>();
</script>
<template>
  <template
        v-for="field in model.attrs"
        :key="field.name"
    >
        <div class="mb-3">
            <AttrsInput :type="field.type" :field="model?.[field.name]" :attributes="field.attributes" :props="field.props" />
        </div>
    </template>
</template>
