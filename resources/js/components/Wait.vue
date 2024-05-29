<script setup lang="ts">
import { ref, onUnmounted } from 'vue';

type Props = {
    time: number
}

const { time } = defineProps<Props>()

const active = ref<boolean>(false)

const timer = setTimeout(() => {
    active.value = true
}, time)

onUnmounted(() => {
    clearTimeout(timer)
})
</script>
<template>
    <template v-if="active">
        <slot></slot>
    </template>
</template>
