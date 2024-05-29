<script setup lang="ts">
import { inject, ref, nextTick } from "vue";
import Nodes from "./Nodes.vue";
import ContentLoader from "@/components/ContentLoader.vue";
import Icon from '@/components/Icon.vue';
import { mdiMinusThick, mdiPlusThick } from "@mdi/js";

type Props = {
  model: any;
  index: number;
  refresh: () => void;
  level?: number;
  defaultMsg?: boolean;
};

const {
  model,
  index,
  refresh,
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

const expanded = ref<boolean>(false);
const children = ref<any>([]);
const loader = ref<any>(null);

const toggle = () => {
  expanded.value = !expanded.value;
  nextTick(() => {
    if (expanded.value && !children.length) {
      fetch();
    }
  });
};

const dataFactory: any = inject("dataFactory");

const fetch = () => {
  loader.value?.start();
  dataFactory({ parent: model, level: level + 1, index }, (data: any) => {
    children.value = data;
    loader.value?.stop();
  });
};

const doRefresh = () => {
  children.value = [];
  nextTick(() => {
    fetch();
  });
};
</script>
<template>
  <div class="node" :class="{ expanded }">
    <div class="node-header">
      <div class="prefix">
        <div class="toggle-icon" @click.stop="toggle">
          <Icon :path="expanded?mdiMinusThick:mdiPlusThick" />
        </div>
      </div>
      <div class="title">
        <slot
          name="default"
          :model="model"
          :level="level"
          :index="index"
          :refresh="refresh"
        ></slot>
      </div>
    </div>
    <div v-if="expanded" class="node-body">
      <ContentLoader ref="loader" title="Data" class="ps-4">
        <template v-if="children.length > 0">
          <Nodes
            :parent="model"
            :children="children"
            :level="level + 1"
            :default-msg="defaultMsg"
            :refresh="doRefresh"
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
        </template>
        <template v-else-if="defaultMsg">
          <div class="text-muted px-3">There are no children yet</div>
        </template>
      </ContentLoader>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";

.node {
  position: relative;

  .node-header {
    min-height: 40px;
    display: flex;
    flex-direction: row;
    align-items: center;
    user-select: none;

    .prefix {
      width: 40px;
      min-width: 40px;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;

      .toggle-icon {
        width: 40px;
        min-width: 40px;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        color: $gray-5;
        @include font-size(1.5);
        transition: all 0.25s ease;

        &:hover {
          color: $primary;
        }
      }
    }

    .title {
      flex: auto;
      display: flex;
      flex-direction: row;
    }
  }

  .node-body {
    position: relative;
    padding: 15px 0 15px 15px;

    &:before {
      content: "";
      display: block;
      position: absolute;
      top: 0;
      left: 19px;
      width: 2px;
      height: 100%;
      background: $gray-5;
    }
  }

  &:hover {
    > .node-header {
      .prefix {
        .toggle-icon {
          color: $primary;
        }
      }

      > .title {
        color: $black;
      }
    }

    > .node-body {
      &:before {
        background: $primary;
      }
    }
  }
}
</style>
