<script setup lang="ts" generic="T extends Model = Model">
import Button from "@/modules/bootstrap/components/Button.vue";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import { ModalFullscreen, ModalSize } from "@/modules/bootstrap/types";
import { ref, onMounted, nextTick } from "vue";
import { Model } from "../../lib/model";
import { IConstructor, createModel } from "../../hooks/model";
import Icon from "@/components/Icon.vue";
import { mdiSend } from "@mdi/js";

defineOptions({
  inheritAttrs: false,
});

type Slots = {
  default?: (props: { model: T }) => void;
  header?: any;
};
defineSlots<Slots>();

type Props = {
  modelClass: IConstructor<T>;
  name: string;
  size?: null | ModalSize;
  fullscreen?: boolean | ModalFullscreen;
};
const {
  modelClass,
  name,
  size = null,
  fullscreen = false,
} = defineProps<Props>();

type Emits = {
  save: [model: T, done: (onlyStop?: boolean) => void];
};
const emit = defineEmits<Emits>();

const modal = ref<any>(null);
const button = ref<any>(null);

const [model, newModel, destroyModel] = createModel(modelClass);

const open = <Item>(data?: Item) => {
  newModel({ ...(data ?? {}) });
  nextTick(() => {
    modal.value?.open?.();
  });
};

const send = () => {
  model.value?.validate?.()?.then?.((valid: boolean) => {
    if (valid && model.value) {
      button.value?.start?.();
      emit("save", model.value, (onlyStop: boolean = false) => {
        button.value?.stop?.();
        if (!onlyStop) {
          nextTick(() => {
            modal.value?.close?.();
          });
        }
      });
    }
  });
};

const unload = () => {
  destroyModel();
};

defineExpose({ open });
</script>
<template>
  <Modal
    v-if="model"
    ref="modal"
    tag="form"
    :title="`${model.isNewRecord ? `Add New` : `Edit`} ${name}`"
    :size="size"
    :fullscreen="fullscreen"
    @submit.prevent="send"
    @closed="unload"
  >
    <template #header>
      <slot name="header"></slot>
    </template>
    <slot :model="model"></slot>
    <template #buttons>
      <Button
        type="submit"
        ref="button"
        color="primary"
        spinner-color="info"
        style="height: 42px"
      >
        <Icon :path="mdiSend" color="info" />Save
      </Button>
    </template>
  </Modal>
</template>
