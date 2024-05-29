<script setup lang="ts">
import Button from "@/modules/bootstrap/components/Button.vue";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import { ref } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiSearchWeb } from "@mdi/js";
import Seo from "./Seo.vue";

defineOptions({
  inheritAttrs: false,
});

export type Props = {
  target?: string;
  title?: string;
};
const { target = "", title = "" } = defineProps<Props>();

const modal = ref<any>(null);

const open = () => {
  modal.value?.open();
};
</script>
<template>
  <Button v-bind="$attrs" @click="open">
    <Icon :path="mdiSearchWeb" /> Seo
  </Button>
  <Modal ref="modal" :title="`${title ?? target} SEO`" size="xl">
    <Seo :target="target"></Seo>
  </Modal>
</template>
