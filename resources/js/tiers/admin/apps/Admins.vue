<script setup lang="ts">
import { User } from "@/forms/user";
import { api } from "@/lib/api";
import { metadata, translate } from "@/lib/metadata";
import Button from "@/modules/bootstrap/components/Button.vue";
import Crud from "@/modules/crud/components/Crud.vue";
import { createFields, fetch } from "@/modules/crud/lib/crud";
import { Field } from "@/modules/form/lib/field";
import { Model } from "@/modules/form/lib/model";
import { RequiredRule } from "@/modules/form/rules/required";
import { StringRule } from "@/modules/form/rules/string";
import { DataList } from "@/modules/form/types";
import Icon from "@/components/Icon.vue";
import { mdiPencil, mdiPlusThick } from "@mdi/js";
import ModalForm from "@/modules/form/components/helper/ModalForm.vue";
import { ref } from "vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import RadioInput from "@/modules/form/components/RadioInput.vue";
import { FieldType } from "@/modules/crud/lib/crud";
import { successToast } from '@/modules/bootstrap/lib/toast';

type Item = {
  id?: number;
  name?: string;
  username?: string;
  user: {
    id?: number;
    username?: string;
    status?: "enabled" | "disabled" | "pending";
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
    name: "name",
    label: "Name",
    fitable: false,
    sortable: true,
    searchable: true,
  },
  {
    type: FieldType.String,
    name: "username",
    label: "Username",
    fitable: true,
    sortable: true,
    searchable: true,
  },
  {
    type: FieldType.String,
    name: "status",
    label: "Status",
    fitable: true,
    sortable: true,
    searchable: true,
  }
);

class Admin extends Model {
  get id(): Field {
    return this.field("id", translate("ID"), this.initData?.id ?? "");
  }

  get name(): Field {
    return this.field("name", translate("Name"), this.initData?.name ?? "");
  }

  get user(): User {
    return this.hasOne("user", (initData: DataList) => new User(initData));
  }

  get rules(): any[] {
    return [
      [[this.name], StringRule],
      [[this.name], RequiredRule],
    ];
  }
}

const crud = ref<any>(null);
const modal = ref<any>(null);

const add = () => {
  modal.value?.open?.();
};

const edit = (data: Item) => {
  modal.value?.open?.(data);
};

const save = (model: Admin, done: () => void) => {
  let request;
  if (model.isNewRecord) {
    request = api().post("/admins", model.data);
  } else {
    request = api().put(`/admins/${model.id.value}`, model.data);
  }
  request.then(() => {
    successToast(`${model.name.value}`, `Admin Saved Successfully`);
    crud.value?.refresh();
    done();
  });
};
</script>
<template>
  <Crud ref="crud" :fields="fields" :fetch="fetch < Item > ('/admins', 'user')">
    <template #usernameValue="{ item }">
      {{ item.user?.username ?? "--" }}
    </template>
    <template #statusValue="{ item }">
      <span :class="{'text-success': item.user?.status === 'enabled'}">{{ item.user?.status }}</span>
    </template>
    <template #actions="{ item }">
      <Button color="info" size="sm" @click="edit(item)">
        <Icon :path="mdiPencil" color="light" />
        <span class="text-light">Edit</span>
      </Button>
    </template>
    <template #topActions>
      <Button color="primary" @click="add">
        <Icon :path="mdiPlusThick" color="light" /> Add New Admin
      </Button>
    </template>
  </Crud>
  <ModalForm
    name="Admin"
    :model-class="Admin"
    size="lg"
    ref="modal"
    @save="save"
  >
    <template #="{ model }">
      <div class="h6 mb-3">User Information</div>
      <div class="mb-4">
        <StringInput :field="model.name" />
      </div>
      <div class="h6 mb-3">Login Authentication</div>
      <div class="mb-4">
        <StringInput :field="model.user.username" />
      </div>
      <div class="row mb-4">
        <div class="col-md-6">
          <StringInput :field="model.user.password" type="password" />
        </div>
        <div class="col-md-6">
          <StringInput :field="model.user.confirmPassword" type="password" />
        </div>
      </div>
      <div>
        <RadioInput
          :field="model.user.status"
          :options="[
            {
              value: 'enabled',
              label: 'Enabled',
            },
            {
              value: 'disabled',
              label: 'Disabled',
            },
            {
              value: 'pending',
              label: 'Pending',
            },
          ]"
          inline
        />
      </div>
    </template>
  </ModalForm>
</template>
