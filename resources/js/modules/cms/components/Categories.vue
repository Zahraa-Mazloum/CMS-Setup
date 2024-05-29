<script setup lang="ts">
import { Category } from "@/modules/cms/forms/category";
import { api } from "@/lib/api";
import Button from "@/modules/bootstrap/components/Button.vue";
import ModalForm from "@/modules/form/components/helper/ModalForm.vue";
import { ref, computed } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiCogOutline, mdiPencil, mdiPlusThick, mdiTranslate } from "@mdi/js";
import Tree from "@/modules/tree/Tree.vue";
import Navs from "@/modules/bootstrap/components/Navs.vue";
import { languages } from "@/lib/metadata";
import TabContent from "@/modules/bootstrap/components/TabContent.vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import TextInput from "@/modules/form/components/TextInput.vue";
import AttrsInput from "@/modules/cms/components/AttrsInput.vue";
import RadioInput from "@/modules/form/components/RadioInput.vue";
import Ref from "@/components/Ref.vue";
import FriendlyUrlModal from "@/modules/friendlyurl/components/FriendlyUrlModal.vue";
import SeoModal from "@/modules/seo/components/SeoModal.vue";
import { na } from "@/lib/helper/strings";

type Props = {
  type: string;
  id?: number;
};

const { type, id = 0 } = defineProps<Props>();

const modal = ref<any>(null);
const tree = ref<any>(null);
const refresher = ref<any>(null);
const tabContent = ref<any>(null);

const factory = (
  args: { parent: any; level: number; index: number },
  callback: (data: any) => void
) => {
  fetch(args.parent).then((data: any) => {
    callback(data);
  });
};

const fetch = (parent: any) => {
  return new Promise((resolve) => {
    api()
      .get(
        `/categories?expand=local,locals&filter[type][like]=${
          id > 0 ? `[${id}]` : ``
        }${type}&filter[parent_id]=${
          parent?.id ?? null
        }&sort=position,local.name`
      )
      .then(({ data }: any) => {
        resolve(data);
      });
  });
};

const add = (refresh, parent: any = null) => {
  refresher.value = refresh;
  modal.value?.open?.({
    type: `${id > 0 ? `[${id}]` : ``}${type}`,
    parent_id: parent?.id ?? null,
    path: parent?.id
      ? `${[parent.id]
          .reduce(
            (list, item) => {
              list.push(item);
              return list;
            },
            parent.path ? parent.path.split(",") : []
          )
          .join(",")}`
      : null,
  });
};

const edit = (refresh, data: any) => {
  refresher.value = refresh;
  modal.value?.open?.(data);
};

const remove = (data: any) => {};

const save = (model: Category, done: () => void) => {
  let request;
  if (model.isNewRecord) {
    request = api().post("/categories", model.data);
  } else {
    request = api().put(`/categories/${model.id.value}`, model.data);
  }
  request.then(() => {
    done();
    if (refresher.value) {
      refresher.value();
      refresher.value = null;
    }
  });
};

const tabs = computed(() => {
  return [
    ...languages().map((language: any) => {
      return {
        name: language.value,
        label: language.label,
        icon: mdiTranslate,
      };
    }),
    { name: "settings", label: "Settings", icon: mdiCogOutline },
  ];
});

const statusList = computed(() => {
  return [
    {
      value: "active",
      label: "Active",
    },
    {
      value: "draft",
      label: "Draft",
    },
    {
      value: "pending",
      label: "Pending",
    },
    {
      value: "deleted",
      label: "Deleted",
    },
  ];
});

const select = (tab: any) => {
  tabContent.value.select(tab);
};

const realType = computed(() => `${id > 0 ? `[${id}]` : ``}${type}`);
</script>
<template>
  <div class="actions d-flex flex-row gap-3">
    <Button v-if="tree" color="primary" @click="add(tree.refresh)">
      <Icon :path="mdiPlusThick" />Add New Category
    </Button>
    <FriendlyUrlModal
      :target="`category:${realType}`"
      :name="type"
      route="/default/categories"
      :params="{ type: realType }"
    ></FriendlyUrlModal>
    <SeoModal
      :target="`categories:${realType}-`"
      :title="type"
      color="primary"
    ></SeoModal>
  </div>
  <div class="categories">
    <Tree ref="tree" :data-factory="factory" default-msg>
      <template #="{ model, level, index, refresh }">
        <div class="category-node">
          <div class="name">
            <Ref
              :key="model.id"
              :value="model.id"
              :start-with="`cms.categories.${realType}`"
            >
              {{ model.local.title }}
            </Ref>
          </div>
          <div class="node-actions">
            <FriendlyUrlModal
              :target="`category:${model.id}`"
              :name="model.local?.title ?? na()"
              route="/default/content"
              :params="{ id: model.id }"
              color="danger"
              size="sm"
            ></FriendlyUrlModal>
            <SeoModal
              :target="`category:${model.id}-`"
              :title="model?.local?.title"
              color="warning"
              size="sm"
            ></SeoModal>
            <Button color="success" size="sm" @click="add(refresh, model)">
              <Icon :path="mdiPlusThick" />
            </Button>
            <Button color="info" size="sm" @click="edit(refresh, model)">
              <Icon :path="mdiPencil" />
            </Button>
          </div>
        </div>
      </template>
    </Tree>
  </div>
  <ModalForm
    name="Category"
    :model-class="Category"
    size="lg"
    ref="modal"
    @save="save"
  >
    <template #header>
      <Navs
        type="pills"
        :tabs="tabs"
        alignment="center"
        @select="select"
        style="flex: auto"
      ></Navs>
    </template>
    <template #="{ model }">
      <TabContent :tabs="tabs" type="tabs" disable-navs ref="tabContent">
        <template #navs></template>
        <template
          v-for="(language, i) in languages()"
          :key="language.value"
          #[language.value]="{ tab }"
        >
          <div class="box mb-3">
            <div class="h6">{{ language.label }}</div>
            <div
              :style="[
                language.value === 'ar'
                  ? { direction: 'rtl' }
                  : { direction: 'ltr' },
              ]"
            >
              <div class="mb-3">
                <StringInput
                  :field="model.locals[i].title"
                  :style="[
                    language.value === 'ar'
                      ? { direction: 'rtl', 'text-align': 'right' }
                      : { direction: 'ltr', 'text-align': 'left' },
                  ]"
                ></StringInput>
              </div>
              <div class="mb-3">
                <TextInput
                  :field="model.locals[i].excerpt"
                  :style="[
                    language.value === 'ar'
                      ? { direction: 'rtl', 'text-align': 'right' }
                      : { direction: 'ltr', 'text-align': 'left' },
                  ]"
                ></TextInput>
              </div>
              <AttrsInput :model="model.locals[i].attrs" />
            </div>
          </div>
        </template>
        <template #settings="{ tab }">
          <AttrsInput :model="model.attrs" />
          <div class="mb-3">
            <StringInput :field="model.position" type="number"></StringInput>
          </div>
          <div class="mb-3">
            <RadioInput
              :field="model.status"
              :options="statusList"
              inline
            ></RadioInput>
          </div>
        </template>
      </TabContent>
    </template>
  </ModalForm>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";

.actions {
  position: relative;
  margin-bottom: 20px;
  background: $gray-1;
  border-radius: 10px;
  padding: 10px;
  display: flex;
  flex-direction: row;
  align-items: center;
  @include dark {
    background: $gray-8;
  }
}

.categories {
  position: relative;
}

.category-node {
  position: relative;
  display: flex;
  flex-direction: row;
  width: 100%;
  height: 40px;
  align-items: center;
  padding: 0 10px;
  border-radius: 10px;
  transition: all 0.25s ease;

  .name {
    display: flex;
    flex-direction: row;
    align-items: center;
    flex: auto;
    color: $gray-9;
    @include dark {
      color: $gray-1;
    }
  }

  .node-actions {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
    width: fit-content;
    gap: 5px;
  }

  &:hover {
    background: $gray-1;
    @include dark {
      background: $gray-8;
    }
    .name {
      color: $gray-9;
      @include dark {
        color: $gray-1;
      }
    }
  }
}
</style>
