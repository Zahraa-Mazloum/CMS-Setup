ImageUploader
<script setup lang="ts">
import { ref } from "vue";
import { Fancybox } from "@/lib/fancybox";
import ImageUploader from "./ImageUploader.vue";
import SortableJS from "sortablejs";
import { Sortable } from "sortablejs-vue3";
import { autoId } from "@/lib/helper/strings";

type Accept = "image/jpeg" | "image/png" | "image/webp";

interface Props {
  list?: any;
  section?: string;
  crop?: boolean;
  aspectRatio?: number | false | undefined;
  accept?: Array<Accept> | Accept;
  width?: number | null;
  height?: number | null;
  max?: number | false;
  direction?: "row" | "column";
}

const {
  list = [],
  section = "gallery",
  crop = false,
  aspectRatio = false,
  accept = "image/jpeg",
  width = null,
  height = null,
  max = false,
  direction = "row",
} = defineProps<Props>();

const items = ref<any>(list || []);
const version = ref<number>(0);

const emit = defineEmits(["touch", "blur", "update", "remove", "view"]);

const update = (image: any) => {
  items.value.push(image);
  version.value++;
  emit("update", items.value, image);
};

const touch = (type, data) => {
  emit("touch", items.value, type, data);
};

const blur = (base64) => {
  emit("blur", items.value, base64);
};

const remove = (i: number) => {
  const image = items.value[i];
  items.value.splice(i, 1);
  items.value = items.value.filter((item) => item);
  version.value++;
  emit("update", items.value, image);
  emit("remove", items.value, image);
};

const view = (index: number) => {
  const fancybox = Fancybox.show(
    items.value?.map((image: any) => {
      return {
        src: image?.url ?? image,
        type: "image",
        caption:
          typeof image.value === "object"
            ? `Size: <span class="text-primary">${image.value.size}</span>, Dimension: <span class="text-primary">${image.value.width}x${image.value.height}</span>, Bits: <span class="text-primary">${image.value.bits}</span>, Mime: <span class="text-primary">${image.value.mime}</span>`
            : null,
      };
    }),
    {
      startIndex: index,
    }
  );
  emit("view", fancybox);
};

const options: SortableJS.SortableOptions = {
  group: autoId(),
};

const updated = (event: SortableJS.SortableEvent) => {
  const { oldIndex, newIndex } = event;

  const list = [...items.value];
  const element = list.splice(oldIndex, 1).at(0);
  list.splice(newIndex, 0, element);

  items.value = [...list];
  version.value++;
  emit("update", items.value, element);
};
</script>
<template>
  <div class="gallery" :key="version" v-bind="$attrs">
    <Sortable
      :list="items"
      item-key="url"
      class="inner"
      @update="updated"
      :options="options"
    >
      <template #item="{ element, index }">
        <div>
          <ImageUploader
            v-if="element"
            :accept="accept"
            :aspect-ratio="aspectRatio"
            :crop="crop"
            :height="height"
            :section="section"
            :width="width"
            :url="element"
            :gallery="index"
            @add="touch"
            @image="update"
            @done="blur"
            @remove="() => remove(index)"
            @view="view(index)"
          ></ImageUploader>
        </div>
      </template>
    </Sortable>
    <template v-if="!max || (max && items.length < max)">
      <ImageUploader
        :accept="accept"
        :aspect-ratio="aspectRatio"
        :crop="crop"
        :height="height"
        :section="section"
        :width="width"
        @add="touch"
        @image="update"
        @done="blur"
        :class="{'add-item': items.length > 0}"
      ></ImageUploader>
    </template>
  </div>
</template>
<style lang="scss">
.gallery {
  display: flex;
  flex-direction: v-bind(direction);
  flex-wrap: wrap;
  justify-content: center;
  gap: 10px;
  width: 100%;
  .add-item {
    .add-btn {
      height: 50px;
    }
  }
  .inner {
    display: inline-flex;
    flex-direction: v-bind(direction);
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    width: 100%;
    user-select: none;
  }
}
</style>
