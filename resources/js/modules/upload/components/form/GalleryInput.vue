<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import FieldWrapper, {
  GroupSlots,
} from "@/modules/form/components/base/FieldWrapper.vue";
import GalleryUploader from "../GalleryUploader.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Accept = "image/jpeg" | "image/png" | "image/webp";

type Props = {
  field: Field;
  noLabel?: boolean;
  section?: string;
  crop?: boolean;
  aspectRatio?: number | false | undefined;
  accept?: Array<Accept> | Accept;
  width?: number | null;
  height?: number | null;
  max?: number | false;
  direction?: "row" | "column";
};
const {
  field,
  noLabel = false,
  section = "gallery",
  crop = false,
  aspectRatio = false,
  accept = "image/jpeg",
  width = null,
  height = null,
  max = false,
  direction = "row",
} = defineProps<Props>();

const update = (images: any, image: any) => {
  field.update(images);
};

const touch = (images: any, type: any, data: any) => {
  field.touch({ target: { value: images } });
};

const blur = (images: any, base64: any) => {
  field.blur({ target: { value: images } });
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <GalleryUploader
        :list="field.value"
        :accept="accept"
        :aspect-ratio="aspectRatio"
        :crop="crop"
        :height="height"
        :section="section"
        :width="width"
        :max="max"
        :direction="direction"
        @add="touch"
        @update="update"
        @done="blur"
      ></GalleryUploader>
    </template>
  </FieldWrapper>
</template>
