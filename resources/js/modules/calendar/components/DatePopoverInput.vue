<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import { GroupSlots } from "@/modules/form/components/base/FieldWrapper.vue";
import { ref } from "vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import Calendar from "@/modules/calendar/components/Calendar.vue";
import Icon from "@/components/Icon.vue";
import { mdiClose, mdiDelete } from "@mdi/js";
import Popover from "@/modules/bootstrap/components/Popover.vue";
import Button from "@/modules/bootstrap/components/Button.vue";

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
  <Popover
    :title="field.label"
    ref="calendarRef"
    @close="onCloseCalendar"
    trigger="manual"
    width="500px"
    padding="0"
  >
    <template #="{ element }">
      <div :ref="element">
        <StringInput :field="field" :no-label="noLabel" @click="openCalendar">
          <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
            <slot :name="slot" v-bind="{ ...scope }" />
          </template>
        </StringInput>
      </div>
    </template>
    <template #content="{ element }">
      <div :ref="element">
        <Calendar
          :date="field.value"
          @change="setDate"
          @done="closeCalendar"
        ></Calendar>

        <div
          class="d-flex flex-horizontal gap-3 p-3 mt-3 justify-content-end border-top"
        >
          <Button color="dark" @click="closeCalendar">
            <Icon :path="mdiClose" />Close
          </Button>
          <template v-if="cleanable">
            <Button color="danger" @click="clean">
              <Icon :path="mdiDelete" />Remove
            </Button>
          </template>
        </div>
      </div>
    </template>
  </Popover>
</template>
