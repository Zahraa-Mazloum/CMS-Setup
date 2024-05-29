<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import FieldWrapper, {
  GroupSlots,
} from "@/modules/form/components/base/FieldWrapper.vue";
import ImageUploader from "../ImageUploader.vue";

type Accept = "image/jpeg" | "image/png" | "image/x-png" | "image/webp" | "image/svg+xml";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  section?: string;
  crop?: boolean;
  aspectRatio?: number | false | undefined;
  accept?: Array<Accept> | Accept;
  width?: number | null;
  height?: number | null;
};
const props = defineProps<Props>();

const {
  field,
  noLabel = false,
  section = "images",
  crop = false,
  aspectRatio = false,
  accept = "image/jpeg",
  width = null,
  height = null,
} = props;

const update = (image: any) => {
  field.update(image);
};

const touch = (type, data) => {
  field.touch({ target: { value: data } });
};

const blur = (base64) => {
  field.blur({ target: { value: base64 } });
};
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <ImageUploader
        :accept="accept"
        :aspect-ratio="aspectRatio"
        :crop="crop"
        :height="height"
        :section="section"
        :width="width"
        :url="field.value ?? null"
        @add="touch"
        @image="update"
        @done="blur"
      >
        <template #add>
          <slot name="add"></slot>
        </template>
      </ImageUploader>
    </template>
  </FieldWrapper>
</template>
