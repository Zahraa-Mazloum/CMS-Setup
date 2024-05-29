<script setup lang="ts">
import EditorView from '@/modules/editor/components/EditorView.vue';

type Props = {
    content: any;
};
const { content } = defineProps<Props>();
</script>
<template>
    <EditorView :content="content.local?.blocks" />
</template>
