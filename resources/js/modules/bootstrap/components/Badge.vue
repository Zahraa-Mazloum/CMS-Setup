<script setup lang="ts">
import { computed } from 'vue';
import { BadgeColor } from '../types';

type Props = {
    color?: BadgeColor
    bgColor?: null | BadgeColor
    pill?: boolean
}

const { color = "primary", bgColor = null, pill = false } = defineProps<Props>()

const classes = computed(() => {
    const cls: any = {badge:true}
    if(bgColor){
        cls[`text-${color}`] = true
        cls[`bg-${bgColor}`] = true
    }else{
        cls[`text-bg-${color}`] = true
    }
    if(pill){
        cls['rounded-pill'] = true
    }
    return cls
})

</script>
<template>
    <span :class="classes" v-bind="$attrs">
        <slot></slot>
    </span>
</template>
