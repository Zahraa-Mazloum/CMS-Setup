<script setup lang="ts">
import { createModel } from "@/modules/form/hooks/model";
import { onUnmounted } from "vue";
import { AuthorModel } from "./author-model";
import TextInput from "@/modules/form/components/TextInput.vue";
import ImageInput from "@/modules/upload/components/form/ImageInput.vue";
import EditableInput from "@/modules/form/components/EditableInput.vue";
import { keypress } from '../../../../lib/helper/html';

type Props = {
  data?: any;
};

const { data = {} } = defineProps<Props>();

const [model, newModel, destroyModel] = createModel(AuthorModel);

newModel(data);

defineExpose({
  validate: () => model.value?.validate(),
  form: () => model.value?.data,
  messages: () => model.value?.errors,
});

onUnmounted(() => {
  destroyModel();
});
</script>
<template>
  <div v-if="model" class="row py-4 m-3 border">
    <div class="col-md-6">
      <div>
        <EditableInput :field="model.name" @keydown.enter.stop />
        <EditableInput :field="model.bio" @keydown.enter.stop />
      </div>
    </div>
    <div class="col-md-6">
      <ImageInput :field="model.image" />
    </div>
  </div>
</template>
