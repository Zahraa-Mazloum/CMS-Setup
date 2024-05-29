<script setup lang="ts" generic="T extends string = string">
import { ref } from "vue";
import Icon from '@/components/Icon.vue';

type Tab = {
  name: T;
  label: string;
  disabled?: boolean;
  icon?: string | false;
};

type Props = {
  type?: "tabs" | "pills" | "nav";
  tabs: Tab[];
  alignment?: "start" | "center" | "end";
  vertical?: boolean;
  fill?: boolean;
  justified?: boolean;
  responsive?: boolean;
};

const {
  type = "nav",
  tabs,
  alignment = "start",
  vertical = false,
  fill = false,
  justified = false,
  responsive = false,
} = defineProps<Props>();

type Slots = {
  [Property in T as `${Property}-tab`]: (props: { tab: Tab }) => void;
};
defineSlots<Slots>();

const active = ref<string>(tabs[0]?.name ?? "");

type Emits = {
  select: [tab: Tab];
};
const emit = defineEmits<Emits>();

const select = (tab: Tab) => {
  active.value = tab.name;
  emit("select", tab);
};
</script>
<template>
  <ul
    class="nav"
    :class="{
      'nav-tabs': type === 'tabs',
      'nav-pills': type === 'pills',
      'nav-fill': fill,
      'nav-justified': justified,
      'flex-column': vertical,
      'justify-content-start': alignment === 'start',
      'justify-content-center': alignment === 'center',
      'justify-content-end': alignment === 'end',
      'flex-sm-row': responsive,
    }"
    role="tablist"
  >
    <template v-for="(tab, i) in tabs" :key="i">
      <li
        class="navitem"
        :class="{
          'flex-sm-fill': responsive,
          'text-sm-center': responsive,
        }"
      >
        <a
          href="#"
          class="navlink"
          :class="{
            disabled: tab.disabled ?? false,
            active: active === tab.name,
          }"
          :id="`tab-${tab.name}`"
          aria-current="page"
          @click.prevent="select(tab)"
          :disabled="tab.disabled"
        >
          <slot :name="`${tab.name}-tab`" :tab="tab">
            <template v-if="tab.icon">
              <Icon :path="tab.icon" /> &nbsp;</template
            ><span>{{ tab.label }}</span>
          </slot>
        </a>
      </li>
    </template>
  </ul>
</template>
