<script setup lang="ts">
import Node from "./Node.vue";

type Props = {
  parent?: any;
  children: any[];
  level?: number;
  defaultMsg?: boolean;
  refresh: () => void;
};
const {
  parent = null,
  children,
  level = 0,
  defaultMsg = false,
} = defineProps<Props>();

type Slots = {
  default: (props: {
    model: any;
    level: number;
    index: number;
    refresh: () => void;
  }) => void;
};
defineSlots<Slots>();

</script>
<template>
  <div v-if="children.length > 0" class="nodes">
    <template v-for="(model, index) in children" :key="index">
      <Node
        :model="model"
        :index="index"
        :level="level"
        :default-msg="defaultMsg"
        :refresh="refresh"
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
      </Node>
    </template>
  </div>
</template>
<style scoped lang="scss">
.nodes {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 5px;
}
</style>
