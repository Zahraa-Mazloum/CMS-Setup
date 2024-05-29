<script setup lang="ts">
import { Settings, SettingsCategory, settings } from "@/lib/metadata";
import ListGroup from "@/modules/bootstrap/components/ListGroup.vue";
import ListGroupItem from "@/modules/bootstrap/components/ListGroupItem.vue";
import { ref, computed, onMounted } from "vue";
import SettingField from "../components/SettingField.vue";
import { api } from "@/lib/api";
import ContentLoader from "@/components/ContentLoader.vue";

const settingsWithKey = settings()?.map?.((category) => {
  category.items = category.items.map((item) => {
    return { ...item, key: `${category.name}.${item.name}` };
  });
  return category;
}) as Settings;

const setting = ref<Settings>(settingsWithKey);
const selected = ref<string | undefined>(setting.value?.at(0)?.name);
const list = ref<any>(null);
const loader = ref<any>(null);

const select = (category: SettingsCategory) => {
  selected.value = category.name;
};

const category = computed(() =>
  setting.value?.find((category) => category.name === selected.value)
);

const fetch = () => {
  loader.value?.start?.();
  api()
    .get("/settings/system")
    .then(({ data }: any) => {
      list.value = data.reduce((items, item) => {
        items[item.name] = item.value;
        return items;
      }, {});
      loadNotConfigured();
    })
    .finally(() => {
      loader.value?.stop?.();
    });
};

const loadNotConfigured = () => {
  if (!Object.is(list.value, null) && Array.isArray(setting.value)) {
    const configured: Array<string> = [];
    setting.value?.forEach((category) => {
      category.items.forEach((item) => {
        configured.push(`${category.name}.${item.name}`);
      });
    });
    const items = Object.keys(list.value)
      .filter((name: string) => !configured.includes(name))
      .map((name: string) => {
        return {
          type: "string",
          key: name,
          name,
          label: name,
          defaultValue: list.value[name],
        };
      });
    setting.value.push({
      name: "other",
      label: "Others",
      items,
    });
  }
};

onMounted(() => {
  fetch();
});
</script>
<template>
  <ContentLoader v-if="Array.isArray(setting)" ref="loader" title="Settings">
    <div class="row">
      <div class="col-md-3">
        <ListGroup type="actions">
          <template v-for="category in setting" :key="category.name">
            <ListGroupItem
              :active="selected === category.name"
              @click="select(category)"
            >
              {{ category.label }}
            </ListGroupItem>
          </template>
        </ListGroup>
      </div>
      <div v-if="category" class="col-md-9">
        <div class="display-6 mb-3">{{ category.label }}</div>
        <template
          v-for="item in category.items"
          :key="`${category.name}-${item.name}`"
        >
          <SettingField
            v-if="!Object.is(list, null)"
            :item="item"
            :category="category"
            :value="list[`${item.key}`]"
          />
        </template>
      </div>
    </div>
  </ContentLoader>
  <div v-else class="p-3 text-muted">There are no settings</div>
</template>
