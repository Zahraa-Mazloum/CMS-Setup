<script setup lang="ts">
import { Field } from "../lib/field";
import { GroupSlots } from "./base/FieldWrapper.vue";
import FieldWrapper from "@/modules/form/components/base/FieldWrapper.vue";
import { ref, onMounted } from "vue";
import Button from "@/modules/bootstrap/components/Button.vue";
import Icon from "@/components/Icon.vue";
import { mdiClose, mdiPlusThick } from "@mdi/js";
import { Attrs } from "../forms/attrs";
import AttrsInput from "./AttrsInput.vue";
import { DataList } from "../types";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  base?: 10 | 12;
  layout?: any[][];
};
const props = defineProps<Props>();

const { field, noLabel = false, base = 12, layout } = props;

let models: any = [];
const u = ref<number>(0);

const add = (data: DataList = {}) => {
  const fields: any = [];
  layout?.forEach((row) => {
    row.forEach((field) => {
      fields.push(field);
    });
  });
  const model: any = Attrs.generate(
    fields,
    data
  );
  model.events.afterUpdate.listen(() => {
    update();
  })
  models.push(model);
  update();
  u.value++;
};

const remove = (i: number) => {
  models.splice(i, 1);
  models = models.filter((item) => item);
  update();
  u.value++;
};

const update = () => {
  field.update(models.map((model) => model.data));
};

const findField = (model: any, name: string) => {
  model.fields.find((fld) => {
    return fld.name === name;
  });
  return model.fields.find((fld) => fld.name === name);
};

onMounted(() => {
  if (Array.isArray(field.value) && field.value.length > 0) {
    field.value.forEach((data) => {
      add(data);
    });
  } else {
    add();
  }
});
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div :key="u" class="repeater-layout d-flex flex-column gap-3">
        <template v-for="(model, v) in models" :key="v">
          <div class="row">
            <div class="col-md-10">
              <div class="repeater-item d-flex flex-column gap-3">
                <template v-for="(row, i) in layout" :key="i">
                  <div class="row">
                    <template v-for="(fld, c) in row" :key="c">
                      <div :class="{ [`col-md-${base / row.length}`]: true }">
                        <AttrsInput
                          :type="fld.type"
                          :field="findField(model, fld.name)"
                          :attributes="fld.attributes"
                          :props="fld.props"
                        />
                      </div>
                    </template>
                  </div>
                </template>
              </div>
            </div>
            <div class="col-md-2">
              <template v-if="v > 0">
                <Button color="danger" @click="remove(v)">
                  <Icon :path="mdiClose" />
                </Button>
              </template>
              <template v-else>
                <Button color="success" @click="add">
                  <Icon :path="mdiPlusThick" />
                </Button>
              </template>
            </div>
          </div>
        </template>
      </div>
    </template>
  </FieldWrapper>
</template>
<style scoped lang="scss">
.repeater-layout {
  flex: auto;
}
</style>
