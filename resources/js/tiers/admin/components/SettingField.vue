<script setup lang="ts">
import { SettingsCategory, SettingsItem } from "@/lib/metadata";
import AttributesInput from "@/modules/form/components/AttributesInput.vue";
import { createModel } from "@/modules/form/hooks/model";
import { Field } from "@/modules/form/lib/field";
import { Model } from "@/modules/form/lib/model";
import AttrsInput from "@/modules/form/components/AttrsInput.vue";
import Button from "@/modules/bootstrap/components/Button.vue";
import { ref, onUnmounted } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiFloppy } from "@mdi/js";
import { api } from "@/lib/api";
import { successToast } from "@/modules/bootstrap/lib/toast";

type Props = {
  item: SettingsItem;
  category: SettingsCategory;
  value?: string;
};

const { item, category, value = undefined } = defineProps<Props>();

const btn = ref<any>(null);

class Setting extends Model {
  get item(): Field {
    return this.field(item.key ?? `${category.name}.${item.name}`, item.label, value ?? item.defaultValue);
  }

  get rules(): any[] {
    return [];
  }
}

const [model, newModel, destroyModel] = createModel(Setting);

const save = async () => {
  const valid = await model.value?.validate();
  if (valid) {
    btn.value?.start?.();
    api()
      .post("/settings/system", {
        name: model.value?.item.name,
        value: model.value?.item.value,
      })
      .then(() => {
        successToast(item.label, "Saved Successfully");
      })
      .finally(() => {
        btn.value?.stop?.();
      });
  }
};

newModel();

onUnmounted(() => {
  destroyModel();
});
</script>
<template>
  <form @submit.prevent="save">
    <div class="mb-3">
      <AttrsInput
        v-if="model"
        :type="item.type"
        :field="model.item"
        :attributes="item.attributes"
        :props="item.props"
        :icon="item.icon"
      >
        <template #buttonAfter="{ field }">
          <Button type="submit" ref="btn" color="secondary" outline>
            <Icon :path="mdiFloppy" />Save
          </Button>
        </template>
      </AttrsInput>
    </div>
  </form>
</template>
