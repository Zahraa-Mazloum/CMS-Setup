<script setup lang="ts">
import Contents from '@/modules/cms/components/Contents.vue';

type Props = {
    type: string
}

const { type } = defineProps<Props>()
</script>
<template>
    <Contents :type="type"></Contents>
</template>
