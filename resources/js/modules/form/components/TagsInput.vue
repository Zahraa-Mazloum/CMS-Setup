<script lang="ts" setup>
import { random } from "@/lib/helper/numbers";
import { Field } from "../lib/field";
import FieldWrapper, { GroupSlots } from "./base/FieldWrapper.vue";
import { ref, computed } from "vue";
import { api } from "@/lib/api";
import { vCssVars } from "@/directives/css-vars";
import { vDebounce } from "@/directives/debounce";
import Icon from "@/components/Icon.vue";
import { mdiClose } from "@mdi/js";
import { confirm } from "@/modules/bootstrap/lib/confirm";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
};
const props = defineProps<Props>();

const { field, noLabel = false } = props;

const colors = [
  "#F44336",
  "#FF1744",
  "#B71C1C",
  "#EC407A",
  "#880E4F",
  "#F50057",
  "#C51162",
  "#AB47BC",
  "#8E24AA",
  "#4A148C",
  "#AA00FF",
  "#673AB7",
  "#311B92",
  "#6200EA",
  "#651FFF",
  "#3F51B5",
  "#1A237E",
  "#3D5AFE",
  "#283593",
  "#304FFE",
  "#0097A7",
  "#00838F",
  "#006064",
  "#00B8D4",
  "#00897B",
  "#00796B",
  "#00695C",
  "#004D40",
  "#43A047",
  "#388E3C",
  "#1B5E20",
  "#C0CA33",
  "#AFB42B",
  "#827717",
  "#FDD835",
  "#FBC02D",
  "#F9A825",
  "#F57F17",
  "#FF6F00",
  "#6D4C41",
  "#5D4037",
  "#3E2723",
];

const name = ref<string>("");
const results = ref<any>(null);
const items = ref<any>(field.value);

const searchTags = (name: string) => {
  results.value = null;
  api()
    .get(`/tags?filter[name][like]=${name}`)
    .then(({ data }: any) => {
      const ids = items.value.map((item: any) => +item.id);
      results.value = data?.filter((tag: any) => !ids.includes(+tag.id)) ?? [];
    });
};

const canAdd = computed(() => {
  return (
    results.value?.filter?.(
      (item: any) => item.name.toLowerCase() === name.value.toLowerCase()
    )?.length === 0 &&
    items.value?.filter?.(
      (item: any) => item.name.toLowerCase() === name.value.toLowerCase()
    )?.length === 0
  );
});

const selected = computed(() => {
  return (
    items.value?.filter?.(
      (item: any) => item.name.toLowerCase() === name.value.toLowerCase()
    )?.length > 0
  );
});

const add = () => {
  api()
    .post("/tags", {
      name: name.value,
      color: colors[random(0, colors.length - 1)],
    })
    .then(({ data }: any) => {
      data.name = data.name.toLowerCase();
      select(data);
    });
};

const select = (item: any) => {
  items.value.push(item);
  name.value = "";
  results.value = null;
  field.update(items.value);
};

const remove = async (item: any) => {
  const ok = await confirm(`Remove "${item.name}" Tag`, "Are you Sure?");
  if (ok) {
    const index = items.value.findIndex((tag: any) => +tag.id === +item.id);
    if (index >= 0) {
      items.value.splice(index, 1);
      field.update(items.value);
    }
  }
};

const update = (e) => {
  field.update(e.target.value);
};

const touch = (e) => {
  field.touch(e);
};

const blur = (e) => {
  field.blur(e);
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div class="tags">
        <template v-if="items.length > 0">
          <template v-for="tag in items" :key="tag.id">
            <div class="tag" v-css-vars="{ '--color': tag.color }">
              <span class="name">{{ tag.name }}</span>
              <a href="#" @click.prevent="remove(tag)" class="close">
                <Icon :path="mdiClose" />
              </a>
            </div>
          </template>
        </template>
        <div class="input-tags">
          <input
            type="text"
            v-model="name"
            v-debounce:300ms="searchTags"
            placeholder="Search Tags..."
          />
          <div v-if="name" class="results">
            <template v-if="results">
              <template v-for="item in results" :key="item.id">
                <a
                  href="#"
                  v-css-vars="{ '--color': item.color }"
                  @click.prevent="select(item)"
                  class="item"
                  >{{ item.name }}</a
                >
              </template>
              <template v-if="canAdd">
                <a href="#" class="item new" @click.prevent="add"
                  >Add <strong>{{ name }}</strong></a
                >
              </template>
            </template>
            <template v-if="selected">
              <div class="selected text-muted">Already Selected</div>
            </template>
            <template v-else-if="name && !results">
              <div class="loader text-muted">Loading...</div>
            </template>
          </div>
        </div>
      </div>
    </template>
  </FieldWrapper>
</template>
<style scoped lang="scss">
@import "@scss/config";
@import "@scss/mixins/dark";

.tags {
  position: relative;
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 5px;

  .tag {
    display: flex;
    flex-direction: row;
    background: var(--color);
    color: $white;
    border-radius: 5px;
    padding: 5px 7px;
    user-select: none;
    gap: 5px;

    .close {
      color: $white;
      transition: all 0.25s ease;

      &:hover {
        scale: 1.1;
      }
    }
  }

  .input-tags {
    position: relative;

    input {
      border: none;
      background: transparent;
      color: $gray-8;
      height: 100%;

      @include dark {
        color: $gray-2;
      }

      &:focus,
      &:focus-within {
        outline: none;
        box-shadow: none;
      }
    }

    .results {
      position: absolute;
      top: 36px;
      white-space: nowrap;
      min-width: 200px;
      background: $white;
      padding: 10px;
      border-radius: 5px;
      border: 1px solid $gray-2;
      box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
      display: flex;
      flex-direction: column;
      gap: 5px;
      z-index: 999;

      @include dark {
        background: $gray-9;
        border: 1px solid $gray-7;
      }

      .item {
        text-decoration: none;
        display: flex;
        flex-direction: row;
        gap: 2px;
        color: $gray-8;
        transition: all 0.25s ease;
        border-radius: 5px;
        padding: 5px 7px;
        user-select: none;

        &:not(.new) {
          color: var(--color);
        }

        &.new {
          color: $black;
          @include dark {
            color: $white;
          }
        }

        &:hover {
          background: $gray-1;
          @include dark {
            background: $gray-8;
          }
        }
      }
    }
  }
}
</style>
