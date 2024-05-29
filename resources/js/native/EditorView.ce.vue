<script setup lang="ts">
import EditorView from "@/modules/editor/components/EditorView.vue";
import { computed, onMounted, getCurrentInstance, ref } from "vue";

type Props = {
  blocks: any;
};
const props = defineProps<Props>();
const instance = getCurrentInstance();

const blocks = computed(() => {
  if (typeof props.blocks === "string") {
    try {
      return JSON.parse(props.blocks);
    } catch (e) {}
  }
  return props.blocks;
});

onMounted(() => {
  instance!.vnode!.el!.parentNode.host.shadowRoot.appendChild(
    document.querySelector("#main-css")?.cloneNode()
  );
});
</script>
<template>
  <EditorView :content="blocks" />
</template>
