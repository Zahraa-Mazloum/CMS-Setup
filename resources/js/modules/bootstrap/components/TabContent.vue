<script setup lang="ts" generic="T extends string = string">
import { ref } from 'vue';
import Navs from './Navs.vue';

type Tab = {
    name: T
    label: string
    disabled?: boolean
    icon?: string | false
}

type Props = {
    type?: 'tabs' | 'pills' | 'nav'
    tabs: Tab[],
    alignment?: 'start' | 'center' | 'end'
    vertical?: boolean
    fill?: boolean
    justified?: boolean
    responsive?: boolean
    disableNavs?: boolean
}

const { type = "nav", tabs, alignment = "start", vertical = false, fill = false, justified = false, responsive = false, disableNavs = false } = defineProps<Props>()

type Slots = {
  [Property in T]: (props: { tab: Tab }) => void;
};
defineSlots<Slots>();

const active = ref<string>(tabs[0]?.name ?? '')

const select = (tab: Tab) => {
    active.value = tab.name
}

defineExpose({ select })
</script>
<template>
    <template v-if="!disableNavs">
        <Navs
            :type="type"
            :tabs="tabs"
            :alignment="alignment"
            :vertical="vertical"
            :fill="fill"
            :justified="justified"
            :responsive="responsive"
            @select="select"
        ></Navs>
    </template>
    <div class="tab-content">
        <template
            v-for="(tab, i) in tabs"
            :key="i"
        >
            <div
                class="tab-pane fade"
                :class="{ show: active === tab.name, active: active === tab.name }"
                role="tabpanel"
                :aria-labelledby="`tab-${tab.name}`"
                :tabindex="i"
            >
                <slot
                    :name="tab.name"
                    :tab="tab"
                ></slot>
            </div>
        </template>
    </div>
</template>
<style scoped lang="scss">
.tab-content {
    min-height: 100%;
    display: flex;
    flex-direction: row;

    .tab-pane {
        min-height: 100%;
        flex: auto;
    }
}
</style>
