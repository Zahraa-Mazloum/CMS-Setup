<script setup lang="ts">
import { cms, languages, translate } from "@/lib/metadata";
import Crud from "@/modules/crud/components/Crud.vue";
import { FieldType, createFields, fetch } from "@/modules/crud/lib/crud";
import { ref, computed } from "vue";
import Button from "@/modules/bootstrap/components/Button.vue";
import Icon from "@/components/Icon.vue";
import {
  mdiCogOutline,
  mdiDelete,
  mdiFileTree,
  mdiPencil,
  mdiPlusThick,
  mdiRayStart,
  mdiTranslate,
} from "@mdi/js";
import { Content } from "@/modules/cms/forms/content";
import { api } from "@/lib/api";
import ModalForm from "@/modules/form/components/helper/ModalForm.vue";
import Navs from "@/modules/bootstrap/components/Navs.vue";
import TabContent from "@/modules/bootstrap/components/TabContent.vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import TextInput from "@/modules/form/components/TextInput.vue";
import AttrsInput from "@/modules/cms/components/AttrsInput.vue";
import EditorInput from "@/modules/editor/components/EditorInput.vue";
import CheckInput from "@/modules/form/components/checkInput.vue";
import RadioInput from "@/modules/form/components/RadioInput.vue";
import { displayTree } from "@/lib/helper/array";
import { confirm } from "@/modules/bootstrap/lib/confirm";
import { successToast } from "@/modules/bootstrap/lib/toast";
import Wait from "@/components/Wait.vue";
import { icon, na } from "@/lib/helper/strings";
import { maxContents } from "../lib/cms";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import Categories from "./Categories.vue";
import Ref from "@/components/Ref.vue";
import EditorView from "@/modules/editor/components/EditorView.vue";
import SeoModal from "@/modules/seo/components/SeoModal.vue";
import FriendlyUrlModal from "@/modules/friendlyurl/components/FriendlyUrlModal.vue";

type Props = {
  type: string;
  id?: number;
};

const { type, id = 0 } = defineProps<Props>();

const crud = ref<any>(null);
const modal = ref<any>(null);

const categoriesModal = ref<any>(null);
const tabContent = ref<any>(null);
const categories = ref<any>(null);
const textCols = ref<any>([]);

type Item = {
  id?: number;
  type?: string;
  status?: "active" | "draft" | "pendig" | "deleted";
  position?: number;
  attrs?: any;
  local?: {
    id?: number;
    content_id?: number;
    language?: string;
    title?: string;
    excerpt?: string;
    blocks?: any;
    plain_text?: string;
    attrs?: any;
  };
};

const fields = createFields(
  {
    type: FieldType.Number,
    name: "id",
    label: "ID",
    fitable: true,
    sortable: true,
    searchable: true,
  },
  {
    type: FieldType.String,
    name: "title",
    label: "Title",
    searchable: true,
    sortable: true,
  },
  {
    type: FieldType.String,
    name: "status",
    label: "Status",
    fitable: true,
    searchable: true,
    sortable: true,
  },
  {
    type: FieldType.Number,
    name: "position",
    label: "Position",
    fitable: true,
    searchable: true,
    sortable: true,
  },
  {
    type: FieldType.Number,
    name: "type",
    label: "Type",
    fitable: true,
    searchable: true,
    sortable: true,
  }
);

const title = computed(
  () => cms()?.type?.find((item: any) => item.name === type)?.label ?? type
);

const add = () => {
  modal.value?.open?.({ type: `${id > 0 ? `[${id}]` : ``}${type}` });
};
const edit = (data) => {
  const record = { ...data };
  if (record.categories) {
    record.categories = record.categories.map((item: any) => item.id);
  }
  modal.value?.open?.(record);
};
const save = (model: Content, done: () => void) => {
  let request;
  if (model.isNewRecord) {
    request = api().post("/contents", model.data);
  } else {
    request = api().put(`/contents/${model.id.value}`, model.data);
  }
  request.then(() => {
    successToast(
      `${title.value} ${model.locals[0].title.value} Saved Successfully`
    );
    crud.value?.refresh();
    done();
  });
};

const remove = (data: any) => {
  confirm(`Delete ${data.locals[0].title}`).then((confirmed: boolean) => {
    if (confirmed) {
      api()
        .delete(`/contents/${data.id}`)
        .then(() => {
          successToast(
            `Content "${data.locals[0].title}" deleted successfully`
          );
          crud.value?.refresh();
        });
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

const select = (tab: any) => {
  tabContent.value.select(tab);
};

const updateTextCol = (el: any) => {
  textCols.value.push(el);
};

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

const settingsCols = computed(() => {
  if (Array.isArray(categories.value) && categories.value.length > 0) {
    return "col-md-3";
  }
  return "col-md-4";
});

const fetchCategories = () => {
  categories.value = [];
  api()
    .get(
      `/categories?expand=local&filter[type]=${type}&sort=position,local.name`
    )
    .then(async ({ data }: any) => {
      categories.value = displayTree(
        (item: any, prefix: string, level: number, hasChildren: boolean) => {
          return {
            value: item.id,
            label: `${prefix}&nbsp;${item.local.title}`,
            title: item.local.title,
          };
        },
        data,
        {
          delimiter: icon({ path: mdiRayStart }),
        }
      );
    });
};

const openCategories = () => {
  categoriesModal.value?.open?.();
};

const closeCategories = () => {
  fetchCategories();
};

const realType = computed(() => `${id > 0 ? `[${id}]` : ``}${type}`);

fetchCategories();
</script>
<template>
  <Crud
    ref="crud"
    :fields="fields"
    :fetch="
      fetch <
      Item >
      ('/contents',
      'local,locals,categories',
      { filter: { type: { like: realType } } })
    "
  >
    <template #topActions>
      <template v-if="!maxContents(type)">
        <FriendlyUrlModal
          :target="`content:${realType}`"
          :name="type"
          route="/default/contents"
          :params="{ type: realType }"
        ></FriendlyUrlModal>
        <SeoModal
          :target="`contents:${realType}-`"
          :title="type"
          color="primary"
        ></SeoModal>
        <Button color="primary" @click="openCategories">
          <Icon :path="mdiFileTree" color="info" />{{ translate("Categories") }}
        </Button>
        <Button color="primary" @click="add">
          <Icon :path="mdiPlusThick" color="info" />{{
            `${translate("Add New")} ${title}`
          }}
        </Button>
      </template>
    </template>
    <template #actions="{ item }">
      <FriendlyUrlModal
        :target="`content:${item.id}`"
        :name="item.local?.title ?? na()"
        route="/default/content"
        :params="{ id: item.id }"
        color="success"
        size="sm"
      ></FriendlyUrlModal>
      <SeoModal
        :target="`content:${item.id}-`"
        :title="item?.local?.title"
        color="warning"
        size="sm"
      ></SeoModal>
      <Button color="info" class="text-light" @click="edit(item)" size="sm">
        <Icon :path="mdiPencil" color="light" />Edit
      </Button>
      <Button color="danger" class="text-light" @click="remove(item)" size="sm">
        <Icon :path="mdiDelete" color="light" />Delete
      </Button>
    </template>
    <template #idValue="{ item }">
      <Ref
        v-if="item.id"
        :key="item.id"
        :value="item.id"
        :start-with="`cms.contents.${id > 0 ? `${id}.` : ``}${type}`"
      >
        {{ item.id }}
      </Ref>
    </template>
    <template #titleValue="{ item }">
      {{ item.local?.title ?? na() }}
    </template>
    <template #details="{ item }">
      <EditorView :content="item.local?.blocks" />
    </template>
  </Crud>
  <ModalForm
    :name="title"
    :model-class="Content"
    fullscreen
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
        <template
          v-for="(language, i) in languages()"
          :key="language.value"
          #[language.value]="{ tab }"
        >
          <div
            class="row h-100"
            :style="[
              language.value === 'ar'
                ? { direction: 'rtl' }
                : { direction: 'ltr' },
            ]"
          >
            <div class="col-md-3">
              <div class="position-sticky top-0">
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
                      { height: '300px' },
                    ]"
                  ></TextInput>
                </div>
                <AttrsInput :model="model.locals[i].attrs" />
              </div>
            </div>
            <div class="col-md-9" :ref="updateTextCol">
              <Wait v-if="textCols[i]" :time="800">
                <EditorInput
                  :field="model.locals[i].blocks"
                  :rtl="language.value === 'ar'"
                  :altitude="textCols[0].clientHeight - 18"
                  no-label
                ></EditorInput>
              </Wait>
            </div>
          </div>
        </template>
        <template #settings="{ tab }">
          <div class="row h-100">
            <div v-if="categories.length > 0" :class="{ [settingsCols]: true }">
              <div class="categories-checks">
                <CheckInput
                  v-if="categories.length > 0"
                  :field="model.categories"
                  :options="categories"
                ></CheckInput>
              </div>
            </div>
            <div :class="{ [settingsCols]: true }">
              <AttrsInput :model="model.attrs1" />
              <div class="mb-3">
                <StringInput :field="model.position"></StringInput>
              </div>
              <div class="mb-3">
                <RadioInput :field="model.status" inline :options="statusList">
                </RadioInput>
              </div>
            </div>
            <div
              v-if="model.attrs2.length > 0"
              :class="{ [settingsCols]: true }"
            >
              <AttrsInput :model="model.attrs2" />
            </div>
            <div
              v-if="model.attrs3.length > 0"
              :class="{ [settingsCols]: true }"
            >
              <AttrsInput :model="model.attrs3" />
            </div>
          </div>
        </template>
      </TabContent>
    </template>
  </ModalForm>
  <Modal
    ref="categoriesModal"
    :title="`Categories of ${title} `"
    size="lg"
    @close="closeCategories"
  >
    <Categories :type="type" :id="id"></Categories>
  </Modal>
</template>
