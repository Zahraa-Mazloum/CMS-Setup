<script setup lang="ts">
import { api } from "@/lib/api";
import { na, nl2br } from "@/lib/helper/strings";
import { languages } from "@/lib/metadata";
import { createQueueIteration } from "@/lib/queue-iteration";
import Badge from "@/modules/bootstrap/components/Badge.vue";
import Button from "@/modules/bootstrap/components/Button.vue";
import { successToast } from "@/modules/bootstrap/lib/toast";
import Crud from "@/modules/crud/components/Crud.vue";
import { createFields, fetch, FieldType } from "@/modules/crud/lib/crud";
import StringInput from "@/modules/form/components/StringInput.vue";
import TextInput from "@/modules/form/components/TextInput.vue";
import ModalForm from "@/modules/form/components/helper/ModalForm.vue";
import { Field } from "@/modules/form/lib/field";
import { Model } from "@/modules/form/lib/model";
import { ref, computed } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiPencil, mdiPlusThick } from "@mdi/js";

export type Props = {
  target?: string;
};
const { target } = defineProps<Props>();

type ITEM = {
  id?: number;
  target?: string;
  title?: string;
  description?: string;
  keywords?: string;
  is_default?: boolean;
};

const fields = createFields(
  {
    type: FieldType.Number,
    name: "id",
    label: "ID",
    sortable: true,
    searchable: true,
    fitable: true,
  },
  {
    type: FieldType.String,
    name: "title",
    label: "Title",
    sortable: true,
    searchable: true,
  },
  {
    type: FieldType.String,
    name: "description",
    label: "Description",
    sortable: true,
    searchable: true,
  },
  {
    type: FieldType.String,
    name: "keywords",
    label: "Keywords",
    sortable: true,
    searchable: true,
    fitable: true,
  },
  {
    type: FieldType.String,
    name: "target",
    label: "Page",
    sortable: true,
    searchable: true,
    fitable: true,
  }
);

const crud = ref<any>(null);
const modal = ref<any>(null);
const adder = ref<any>(null);
const isEmpty = ref<boolean>(false);

class Seo extends Model {
  get id(): Field {
    return this.field("id", "ID", this.initData?.id ?? null);
  }

  get target(): Field {
    return this.field("target", "Target", this.initData?.target ?? null);
  }

  get title(): Field {
    return this.field("title", "Title", this.initData?.title ?? "");
  }

  get description(): Field {
    return this.field(
      "description",
      "Description",
      this.initData?.description ?? ""
    );
  }

  get keywords(): Field {
    return this.field(
      "keywords",
      "Keywords",
      this.initData?.keywords ?? [],
      "Separate keywords by comma"
    );
  }

  get rules(): any[] {
    return [];
  }
}

const setEmpty = (value: boolean) => {
  isEmpty.value = value;
};

const add = () => {
  adder.value?.start?.();
  const data = crud.value?.data?.()?.map((item: ITEM) => item.target?.split('-').at(1));
  const langs = languages().filter(({ value }) => !data.includes(value))
  const queue = createQueueIteration(langs.length);
  queue.action = (index, next) => {
    api()
      .post("/seo", {
        target: `${target}${langs?.at?.(index)?.value}`,
        keywords: [],
        description: "",
        title: `Seo Title ${new Date().getTime()}`,
      })
      .then(() => {
        next();
      });
  };
  queue.on("done", () => {
    successToast("Seo Created Successfully");
    crud.value?.refresh?.();
    adder.value?.stop?.();
  });
  queue.start();
};

const edit = (data) => {
  const record = { ...data };
  record.keywords = record.keywords.join("\n");
  modal.value?.open?.(record);
};

const save = (model: Seo, done: (onlyStop?: boolean) => void) => {
  const data = model.data;
  data.keywords = data.keywords?.split?.(",") ?? [];
  api()
    .put(`/seo/${data.id}`, data)
    .finally(() => {
      successToast("Seo Created Successfully");
      crud.value?.refresh?.();
      done();
    }).catch((err) => {
    done(true);
    model?.addRemoteErrors?.(err);
  });
};

const params = computed(() => {
  if (target) {
    return { filter: { target: { like: target } } };
  }
  return undefined;
});

const notAllInserted = computed(() => {
  return crud.value?.length?.() < languages()?.length;
});
</script>
<template>
  <Crud
    ref="crud"
    :fields="fields"
    :fetch="fetch < ITEM > ('/seo', undefined, params)"
    @empty="setEmpty(true)"
    @no-empty="setEmpty(false)"
  >
    <template #descriptionValue="{ item }">
      <div v-html="nl2br(item?.description ?? '')"></div>
    </template>
    <template #targetValue="{ item }">
      <div class="d-flex flex-horizontal gap-2">
        <Badge
          color="primary"
          v-if="item?.target?.split?.('-')?.[0]?.toString()?.split?.(':')?.[0]"
          >{{
            item?.target?.split?.("-")?.[0]?.toString()?.split?.(":")?.[0] ?? ""
          }}</Badge
        >
        <Badge color="info" v-if="item?.target?.split?.('-')?.[1]">{{
          item?.target?.split?.("-")?.[1] ?? ""
        }}</Badge>
      </div>
    </template>
    <template #keywordsValue="{ item }">
      <div
        v-if="Array.isArray(item.keywords) && item.keywords.length > 0"
        class="d-flex flex-horizontal gap-2"
      >
        <template v-for="keyword in item.keywords" :key="keyword">
          <Badge color="warning">{{ keyword }}</Badge>
        </template>
      </div>
      <div v-else v-html="na()"></div>
    </template>
    <template #actions="{ item }">
      <Button color="info" class="text-light" @click="edit(item)">
        <Icon :path="mdiPencil" color="light" />Edit
      </Button>
    </template>
    <template #topActions>
      <Button
        ref="adder"
        v-if="target && (isEmpty || notAllInserted)"
        color="primary"
        class="text-light"
        @click="add"
      >
        <Icon :path="mdiPlusThick" color="light" />Add Seo
      </Button>
    </template>
  </Crud>
  <ModalForm ref="modal" name="SEO" :model-class="Seo" @save="save">
    <template #="{ model }">
      <div class="mb-3">
        <StringInput :field="model.title" />
      </div>
      <div class="mb-3">
        <TextInput :field="model.description" style="height: 200px" />
      </div>
      <div class="mb-3">
        <TextInput :field="model.keywords" style="height: 200px" />
      </div>
    </template>
  </ModalForm>
</template>
