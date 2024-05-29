<script setup lang="ts">
import Button from "@/modules/bootstrap/components/Button.vue";
import { ref } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiLink } from "@mdi/js";
import ModalForm from "@/modules/form/components/helper/ModalForm.vue";
import { Model } from "@/modules/form/lib/model";
import { api } from "@/lib/api";
import { nl2br } from "@/lib/helper/strings";
import { Field } from "@/modules/form/lib/field";
import StringInput from "@/modules/form/components/StringInput.vue";
import { successToast } from "@/modules/bootstrap/lib/toast";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  target: string;
  name: string;
  route: string;
  params: { [name: string]: any };
};
const { target, name, route, params } = defineProps<Props>();

const btn = ref<any>(null);
const modal = ref<any>(null);

class FriendlyUrl extends Model {
  get id(): Field {
    return this.field("id", "ID", this.initData?.id ?? null);
  }

  get target(): Field {
    return this.field("target", "Target", this.initData?.target ?? "default");
  }

  get name(): Field {
    return this.field("name", "Name", this.initData?.name ?? "");
  }

  get pattern(): Field {
    return this.field("pattern", "Pattern", this.initData?.pattern ?? "");
  }

  get route(): Field {
    return this.field("route", "Route", this.initData?.route ?? "");
  }

  get params(): Field {
    return this.field("params", "Params", this.initData?.params ?? {});
  }

  get rules(): any[] {
    return [];
  }
};

const open = () => {
  btn.value?.start?.();
  api()
    .get(`/friendlyUrl?filter[target]=${target}`)
    .then(({ data }: any) => {
      modal.value?.open?.(
        data[0] ?? {
          target,
          name,
          route,
          params,
        }
      );
    })
    .finally(() => {
      btn.value?.stop?.();
    });
};

const save = (model: FriendlyUrl, done: (onlyStop?: boolean) => void) => {
  let request;
  if (model.isNewRecord) {
    request = api().post("/friendlyUrl", model.data);
  } else {
    request = api().put(`/friendlyUrl/${model.id.value}`, model.data);
  }
  request.then(() => {
    successToast(
      `${name} url Saved Successfully`
    );
    done();
  }).catch((err) => {
    done(true);
    model?.addRemoteErrors?.(err);
  })
};
</script>
<template>
  <Button ref="btn" @click="open" v-bind="$attrs" spinner-small> <Icon :path="mdiLink"></Icon>Url</Button>
  <ModalForm
    ref="modal"
    :name="nl2br(name)"
    :model-class="FriendlyUrl"
    @save="save"
  >
    <template #="{ model }">
      <StringInput :field="model.pattern" />
    </template>
  </ModalForm>
</template>
