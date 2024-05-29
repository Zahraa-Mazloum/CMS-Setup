<script lang="ts">
const cache: any = {};
const components: any = [];
</script>
<script setup lang="ts">
import { api } from "@/lib/api";
import {
  onMounted,
  ref,
  getCurrentInstance,
  ComponentInternalInstance,
  nextTick,
} from "vue";
import ContentLoader from "./ContentLoader.vue";
import { ucFirst } from "@/lib/helper/strings";
import ContextMenu from "./ContextMenu.vue";
import { user } from "@/lib/metadata";
import { vTooltip } from "@/modules/bootstrap/directives/tooltip";
import Icon from "@/components/Icon.vue";
import { mdiCheckboxBlankOutline, mdiCheckboxMarkedOutline } from "@mdi/js";

type Props = {
  value: number | string;
  startWith: string;
};

const { value, startWith } = defineProps<Props>();

const instance = getCurrentInstance();
components.push(instance);

const items = ref<any>(null);
const loader = ref<any>(null);
const selectedItem = ref<any>(null);
const contextMenu = ref<any>(null);

const fetch = (force: boolean = false) => {
  return new Promise((resolve) => {
    loader.value?.start();
    items.value = null;
    if (!force && Object.hasOwn(cache, startWith)) {
      nextTick(() => {
        items.value = [...cache[startWith]];
        findSelectedItem();
        loader.value?.stop();
        resolve(items.value);
      });
    } else {
      api()
        .get(`/settings/references?startWith=${startWith}`)
        .then(({ data }: any) => {
          items.value = cache[startWith] = data.map((item: any) => {
            return updateItem(item);
          });
          findSelectedItem();
          resolve(items.value);
        })
        .finally(() => {
          loader.value?.stop();
        });
    }
  });
};

const updateItem = (item: any) => {
  return {
    ...item,
    label: item.name
      .split(".")
      .reverse()
      .map((word: string) => ucFirst(word))
      .join(" "),
  };
};

const findSelectedItem = () => {
  selectedItem.value = null;
  items.value.forEach((item: any) => {
    if (+item.value === +value) {
      selectedItem.value = { ...updateItem(item) };
    }
  });
};

const save = (item: any) => {
  const oldId = selectedItem.value?.id ?? 0;
  const newId = item.id;
  if (oldId !== newId) {
    api()
      .post("/settings/references", { oldId, newId, value })
      .finally(() => {
        contextMenu.value?.close();
        fetch(true).then(() => {
          components.forEach(
            (component: ComponentInternalInstance | null, index: number) => {
              if (component?.uid !== instance?.uid) {
                component?.exposed?.fetch();
              }
            }
          );
        });
      });
    selectedItem.value = { ...item, value };
  }
};

onMounted(() => {
  fetch();
});

defineExpose({ fetch });
</script>
<template>
  <template v-if="user().type === 'developer'">
    <ContextMenu ref="contextMenu">
      <template #="{ open }">
        <span @click.right.prevent="open($event)">
          <slot>{{ value }}</slot
          ><sup
            v-if="selectedItem && +selectedItem.value === +value"
            v-tooltip="`${selectedItem.label}\n${selectedItem.name}`"
            class="text-danger"
            >*</sup
          >
        </span>
      </template>
      <template #contextmenu>
        <ContentLoader ref="loader" title="References">
          <template v-if="items && items.length > 0">
            <ul class="list-group list-group-flush">
              <template v-for="item in items" :key="item.id">
                <li class="list-group-item p-0" @click="save(item)">
                  <div class="item">
                    <template
                      v-if="selectedItem && +selectedItem.value === +item.value"
                    >
                      <Icon :path="mdiCheckboxMarkedOutline" color="success" />
                    </template>
                    <template v-else>
                      <Icon :path="mdiCheckboxBlankOutline" color="muted" />
                    </template>
                    <span class="name">
                      {{ item.label }}
                    </span>
                  </div>
                </li>
              </template>
            </ul>
          </template>
          <template v-else>
            <div class="text-muted">There are no references yet</div>
          </template>
        </ContentLoader>
      </template>
    </ContextMenu>
  </template>
  <template v-else>
    <slot>{{ value }}</slot>
  </template>
</template>
<style scoped lang="scss">
@import "@scss/config";
@import "@scss/mixins/font-size";
@import "@scss/mixins/dark";

.item {
  display: flex;
  flex-direction: row;
  cursor: pointer;
  gap: 5px;
  align-items: center;
  height: 40px;

  .name {
    color: $gray-8;
    @include font-size(1);
    @include dark {
      color: $gray-1;
    }
  }
}
</style>
