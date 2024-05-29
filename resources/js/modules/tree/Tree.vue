<script setup lang="ts">
import { provide, ref, nextTick } from 'vue';
import Nodes from './Nodes.vue';

type Props = {
    dataFactory: (args: { parent: any, level: number, index: number }, callback: (data: any) => void) => void,
    defaultMsg?: boolean
}

const { dataFactory, defaultMsg = false } = defineProps<Props>()

type Slots = {
  default: (props: {
    model: any;
    level: number;
    index: number;
    refresh: () => void;
  }) => void;
};
defineSlots<Slots>();

provide('dataFactory', dataFactory)

const children = ref<any>([])

const fetch = () => {
    dataFactory({ parent: null, level: 0, index: 0 }, (data: any) => {
        children.value = data
    })
}

const refresh = () => {
    children.value = []
    nextTick(() => {
        fetch()
    })
}

fetch()

defineExpose({ refresh })
</script>
<template>
    <div
        v-if="children.length > 0"
        class="tree"
    >
        <Nodes
            :children="children"
            :refresh="refresh"
            :default-msg="defaultMsg"
        >
            <template #="{ model, level, index, refresh }">
                <slot
                    name="default"
                    :model="model"
                    :level="level"
                    :index="index"
                    :refresh="refresh"
                ></slot>
            </template>
        </Nodes>
    </div>
</template>
<style scoped lang="scss">
.tree {
    position: relative;
    &,
    * {
        transition: all 0.25s ease;
    }
}
</style>
