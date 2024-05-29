<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import { GroupSlots } from "@/modules/form/components/base/FieldWrapper.vue";
import { ref } from "vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import Calendar from "@/modules/calendar/components/Calendar.vue";
import Icon from "@/components/Icon.vue";
import { mdiDelete } from "@mdi/js";
import Button from '@/modules/bootstrap/components/Button.vue';

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  noValue?: boolean;
  cleanable?: boolean;
};
const props = defineProps<Props>();

const { field, noLabel = false, noValue = false, cleanable = false } = props;

const initValue = ref<boolean>(!noValue);
const calendarRef = ref<any>(null);

type Emits = {
  open: any;
  change: [value: null | string];
  close: any;
};
const emit = defineEmits<Emits>();

const openCalendar = () => {
  calendarRef.value.open();
  emit("open");
};

const setDate = (dateStr: string) => {
  if (initValue.value) {
    field.update(dateStr);
    emit("change", dateStr);
  }
  initValue.value = true;
};

const onCloseCalendar = () => {
  emit("close", field.value);
};

const closeCalendar = () => {
  calendarRef.value?.close?.();
};

const clean = () => {
  field.update(null);
  emit("change", null);
  closeCalendar();
};

defineExpose({ open: openCalendar, close: closeCalendar });
</script>
<template>
  <StringInput :field="field" :no-label="noLabel" @focus="openCalendar()">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
  </StringInput>
  <Modal
    :title="field.label"
    default-size
    ref="calendarRef"
    @close="onCloseCalendar"
    padding="0"
  >
    <Calendar
      :date="field.value"
      @change="setDate"
      @done="closeCalendar"
    ></Calendar>
    <template v-if="cleanable" #buttons>
      <Button color="danger" @click="clean">
        <Icon :path="mdiDelete" />Remove
      </Button>
    </template>
  </Modal>
</template>
