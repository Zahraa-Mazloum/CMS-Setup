<script setup lang="ts" generic="T extends Model">
import { language, languages } from "@/lib/metadata";
import { Model } from "../../lib/model";
import { ref, computed, nextTick } from "vue";

type Props = {
  models: T[];
};
const { models } = defineProps<Props>();

type Slots = {
  default: (props: { model: T; language: string }) => void;
};
defineSlots<Slots>();

const activeLanguage = ref<string>(language());

const select = (value) => {
  activeLanguage.value = value;
};

const model = computed(() => {
  return models.find((m: any) => m.language.value === activeLanguage.value);
});
</script>
<template>
  <div class="locals">
    <div class="languages">
      <template v-for="lang in languages()" :key="lang.value">
        <a
          href="#"
          :class="{ active: lang.value === activeLanguage }"
          @click.prevent="select(lang.value)"
        >
          {{ lang.label }}
        </a>
      </template>
    </div>
    <div
      :key="activeLanguage"
      :style="[
        activeLanguage === 'ar'
          ? { direction: 'rtl', 'text-align': 'right' }
          : { direction: 'ltr', 'text-align': 'left' },
      ]"
    >
      <slot
        v-if="model && activeLanguage"
        :model="model"
        :language="activeLanguage"
      ></slot>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";

.locals {
  position: relative;
  .languages {
    position: relative;
    display: flex;
    flex-direction: row;
    gap: 5px;
    a {
      text-decoration: none;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      padding: 5px 7px;
      border-radius: 3px;
      &:hover {
        color: $primary;
      }
      &.active {
        background: $primary;
        color: $white;
      }
    }
  }
}
</style>
