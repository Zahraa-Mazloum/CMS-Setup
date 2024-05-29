<script setup lang="ts">
import Button from "@/modules/bootstrap/components/Button.vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import { createModel } from "@/modules/form/hooks/model";
import { Field } from "@/modules/form/lib/field";
import { Model } from "@/modules/form/lib/model";
import { RequiredRule } from "@/modules/form/rules/required";
import { StringRule } from "@/modules/form/rules/string";
import { mdiAccount, mdiFormTextboxPassword, mdiSend } from "@mdi/js";
import { onMounted, onUnmounted, ref } from "vue";
import Icon from "@/components/Icon.vue";
import { api } from "@/lib/api";
import { metadata } from "@/lib/metadata";
import { isDark } from "@/modules/theme";

type Props = {
  redirectUrl?: string;
};

const { redirectUrl = "" } = defineProps<Props>();

class Login extends Model {
  get username(): Field {
    return this.field("username", "Username", this.initData?.username ?? "");
  }

  get password(): Field {
    return this.field("password", "Password", this.initData?.password ?? "");
  }

  get rules(): any[] {
    return [
      [[this.username, this.password], StringRule],
      [[this.username, this.password], RequiredRule],
    ];
  }
}
const button = ref<any>(null);

const [model, newModel, destroyModel] = createModel(Login);

const login = () => {
  model.value?.validate()?.then((valid: boolean) => {
    if (valid) {
      button.value?.start?.();
      api()
        .post("/login", model.value?.data ?? {})
        .then(({ data }: any) => {
          if (data.code === 200) {
            const form = document.createElement("form");
            form.name = "login";
            form.method = "POST";
            form.action = `${metadata()?.url?.self}/Login`;
            form.style.visibility = "hidden";
            form.style.width = "0px";
            form.style.height = "0px";
            form.style.overflow = "hidden";
            form.style.opacity = "0";

            const tokenInput = document.createElement("input");
            tokenInput.type = "hidden";
            tokenInput.name = "token";
            tokenInput.value = data.token;
            form.appendChild(tokenInput);

            const expireInput = document.createElement("input");
            expireInput.type = "hidden";
            expireInput.name = "expire";
            expireInput.value = data.expire;
            form.appendChild(expireInput);

            const redirectUrlInput = document.createElement("input");
            redirectUrlInput.type = "hidden";
            redirectUrlInput.name = "redirectUrl";
            redirectUrlInput.value = redirectUrl ?? "";
            form.appendChild(redirectUrlInput);

            document.body.appendChild(form);
            form.submit();
          }
        })
        .catch((err) => {
          model.value?.addRemoteErrors?.(err);
        })
        .finally(() => {
          button.value?.stop?.();
        });
    }
  });
};

onMounted(() => {
  newModel();
});

onUnmounted(() => {
  destroyModel();
});
</script>
<template>
  <form v-if="model" @submit.prevent="login" class="login-form">
    <div>
      <StringInput :field="model.username">
        <template #before>
          <Icon :path="mdiAccount" :color="isDark() ? 'info' : 'primary'" />
        </template>
      </StringInput>
    </div>
    <div>
      <StringInput :field="model.password" type="password">
        <template #before>
          <Icon
            :path="mdiFormTextboxPassword"
            :color="isDark() ? 'info' : 'primary'"
          />
        </template>
      </StringInput>
    </div>
    <div class="d-flex justify-content-center align-items-center">
      <Button
        type="submit"
        color="primary"
        class="fw-bold w-100"
        spinner-color="light"
        ref="button"
      >
        <Icon :path="mdiSend" color="info" />SIGN IN</Button
      >
    </div>
  </form>
</template>
<style lang="scss">
@import "@scss/config";
@import "@scss/mixins/dark";

.login-form {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 30px;
  width: 100%;
  background: rgba($white, 0.6);
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
  @include dark {
    background: rgba($black, 0.6);
  }
  label {
    color: $info;
    font-weight: bold;
  }
  input {
    height: 50px;
  }
  button {
    height: 50px;
  }
}
</style>
