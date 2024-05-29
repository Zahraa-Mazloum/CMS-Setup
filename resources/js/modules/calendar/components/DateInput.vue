<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import { GroupSlots } from "@/modules/form/components/base/FieldWrapper.vue";
import { ref } from "vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import Calendar from "@/modules/calendar/components/Calendar.vue";
import Icon from "@/components/Icon.vue";
import { mdiDelete } from "@mdi/js";
import Popover from "@/modules/bootstrap/components/Popover.vue";
import DateModalInput from "./DateModalInput.vue";
import DatePopoverInput from "./DatePopoverInput.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  noValue?: boolean;
  cleanable?: boolean;
  popover?: boolean;
};
const props = defineProps<Props>();

const {
  field,
  noLabel = false,
  noValue = false,
  cleanable = false,
  popover = false,
} = props;

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

defineExpose({ openCalendar, closeCalendar });
</script>
<template>
  <template v-if="popover">
    <DatePopoverInput
      ref="calendarRef"
      :field="field"
      :no-label="noLabel"
      :no-value="noValue"
      :cleanable="cleanable"
      @close="onCloseCalendar"
    ></DatePopoverInput>
  </template>
  <template v-else>
    <DateModalInput
      ref="calendarRef"
      :field="field"
      :no-label="noLabel"
      :no-value="noValue"
      :cleanable="cleanable"
      @close="onCloseCalendar"
    ></DateModalInput>
  </template>
</template>
