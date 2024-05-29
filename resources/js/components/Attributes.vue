<script setup lang="ts">
import { ref } from "vue";
import Icon from "@/components/Icon.vue";
import { mdiPlusThick, mdiMinusThick } from "@mdi/js";

type Attribute = {
  name: string;
  value: string;
};

type Props = {
  modelValue: Attribute[];
  fixed?: boolean;
  readOnlyName?: boolean;
  nameAsValue?: boolean;
};

const {
  modelValue,
  fixed = false,
  readOnlyName = false,
  nameAsValue = false,
} = defineProps<Props>();

const emit = defineEmits(["update:modelValue"]);

const list = ref<Attribute[]>(modelValue);

const add = () => {
  list.value.push({
    name: "",
    value: "",
  });
  emit("update:modelValue", list.value);
};

const remove = (index: number) => {
  list.value.splice(index, 1);
  emit("update:modelValue", list.value);
};

const update = (event: any, index: number) => {
  if (nameAsValue) {
    list.value[index].name = event.target.value;
  }
};
</script>
<template>
  <template v-if="Array.isArray(list) && list.length > 0">
    <template v-for="(attribute, i) in list" :key="i">
      <div class="row" :class="{ 'mt-2': i > 0 }">
        <div
          v-if="!nameAsValue"
          :class="{ 'col-md-5': !fixed, 'col-md-6': fixed }"
        >
          <template v-if="readOnlyName">
            <div class="form-control" v-html="attribute.name"></div>
          </template>
          <template v-else>
            <input
              type="text"
              class="form-control"
              v-model="attribute.name"
              placeholder="Name"
            />
          </template>
        </div>
        <div class="col-md-6">
          <input
            type="text"
            class="form-control"
            v-model="attribute.value"
            placeholder="Value"
            @input="update($event, i)"
          />
        </div>
        <div v-if="!fixed" class="col-md-1 d-flex flex-column">
          <template v-if="i === 0">
            <button
              type="button"
              class="btn btn-primary btn-sm w-100 h-100"
              @click="add"
            >
              <Icon :path="mdiPlusThick" />
            </button>
          </template>
          <template v-else>
            <button
              type="button"
              class="btn btn-secondary btn-sm w-100 h-100"
              @click="remove(i)"
            >
              <Icon :path="mdiMinusThick" />
            </button>
          </template>
        </div>
      </div>
    </template>
  </template>
  <template v-else-if="!fixed">
    <div class="d-flex flex-row justify-content-center align-items-center p-3">
      <button type="button" class="btn btn-primary btn-sm w-100" @click="add">
        <Icon :path="mdiPlusThick" />&nbsp;Create First Attribute
      </button>
    </div>
  </template>
</template>
