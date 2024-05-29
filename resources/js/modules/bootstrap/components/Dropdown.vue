<script setup lang="ts">
import Button from "@/modules/bootstrap/components/Button.vue";
import { DropdownDirection, DropdownAlignment } from "../types";
import Dropdown from "bootstrap/js/src/dropdown";
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import Wrapper from "@/components/Wrapper.vue";

defineOptions({
  inheritAttrs: false,
});

type Slots = {
  button: any;
  default: any;
};
defineSlots<Slots>();

type Props = {
  title?: string;
  split?: boolean;
  center?: boolean;
  direction?: DropdownDirection;
  alignment?: DropdownAlignment;
  smStart?: boolean;
  mdStart?: boolean;
  lgStart?: boolean;
  xlStart?: boolean;
  xxlStart?: boolean;
  smEnd?: boolean;
  mdEnd?: boolean;
  lgEnd?: boolean;
  xlEnd?: boolean;
  xxlEnd?: boolean;
  autoClose?: boolean | "inside" | "outside";
  offset?: [number, number];
  menuOptions?: { [name: string]: any };
  div?: boolean;
  flat?: boolean;
  noArrow?: boolean;
};
const {
  title = "",
  split = false,
  center = false,
  direction = "down",
  alignment = "start",
  smStart = false,
  mdStart = false,
  lgStart = false,
  xlStart = false,
  xxlStart = false,
  smEnd = false,
  mdEnd = false,
  lgEnd = false,
  xlEnd = false,
  xxlEnd = false,
  autoClose = false,
  offset = [0, 2],
  menuOptions = {},
  div = false,
  flat = false,
  noArrow = false,
} = defineProps<Props>();

const dropdownRef = ref<any>(null);
const dropdown = ref<any>(null);

watch(
  () => dropdownRef,
  (newRef) => {
    if (newRef) {
      create();
    }
  },
  { deep: true }
);

type Emits = {
  close: any;
  closed: any;
  prevented: any;
  open: any;
  opened: any;
};
const emit = defineEmits<Emits>();

const onClose = (e) => {
  emit("close", e);
};

const onClosed = (e) => {
  emit("closed", e);
};

const onOpen = (e) => {
  emit("open", e);
};

const onOpened = (e) => {
  emit("opened", e);
};

const create = () => {
  dropdown.value = new Dropdown(dropdownRef.value, { autoClose, offset });
  dropdownRef.value.addEventListener("hide.bs.dropdown", onClose);
  dropdownRef.value.addEventListener("hidden.bs.dropdown", onClosed);
  dropdownRef.value.addEventListener("show.bs.dropdown", onOpen);
  dropdownRef.value.addEventListener("shown.bs.dropdown", onOpened);
};

const open = () => {
  dropdown.value?.show?.();
};

const close = () => {
  dropdown.value?.hide?.();
};

const dispose = () => {
  dropdownRef.value.removeEventListener("hide.bs.dropdown", onClose);
  dropdownRef.value.removeEventListener("hidden.bs.dropdown", onClosed);
  dropdownRef.value.removeEventListener("show.bs.dropdown", onOpen);
  dropdownRef.value.removeEventListener("shown.bs.dropdown", onOpened);
  dropdown.value?.dispose?.();
};

const update = () => {
  dropdown.value?.update?.();
};

defineExpose({
  open,
  close,
  dispose,
  update,
  tagName: () => (div ? "div" : "ul"),
});

const menuClasses = computed(() => {
  return {
    "dropdown-menu": true,
    [`dropdown-menu-${alignment}`]: true,
    ...(smStart ? { "dropdown-menu-sm-start": true } : {}),
    ...(mdStart ? { "dropdown-menu-md-start": true } : {}),
    ...(lgStart ? { "dropdown-menu-lg-start": true } : {}),
    ...(xlStart ? { "dropdown-menu-xl-start": true } : {}),
    ...(xxlStart ? { "dropdown-menu-xxl-start": true } : {}),
    ...(smEnd ? { "dropdown-menu-sm-end": true } : {}),
    ...(mdEnd ? { "dropdown-menu-md-end": true } : {}),
    ...(lgEnd ? { "dropdown-menu-lg-end": true } : {}),
    ...(xlEnd ? { "dropdown-menu-xl-end": true } : {}),
    ...(xxlEnd ? { "dropdown-menu-xxl-end": true } : {}),
  };
});

onMounted(() => {
  // create();
});

onUnmounted(() => {
  dispose();
});
</script>
<template>
  <Wrapper
    :tag="flat ? 'div' : false"
    :class="{
      'btn-group': true,
      [`drop${direction}${center ? '-center' : ''}`]: true,
    }"
  >
    <Button
      :class="{ 'dropdown-toggle': !split, 'no-arrow': noArrow }"
      :data-bs-toggle="!split ? 'dropdown' : false"
      aria-expanded="false"
      @click="open"
      :ref="
        (el: any) => {
          if (!split && el) {
            dropdownRef = el.element();
          }
        }
      "
      v-binds="$attrs"
    >
      <slot name="button"> {{ title }} </slot>
    </Button>
    <template v-if="split">
      <Button
        class="dropdown-toggle dropdown-toggle-split"
        :class="{ 'no-arrow': noArrow }"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        @click="open"
        v-binds="$attrs"
        :ref="(el: any) => {
          if (!split && el) {
            dropdownRef = el.element();
          }
        }"
      >
        <span class="visually-hidden">Toggle Dropdown</span>
      </Button>
    </template>
    <component
      :is="div ? 'div' : 'ul'"
      :class="menuClasses"
      v-binds="menuOptions"
    >
      <slot></slot>
    </component>
  </Wrapper>
</template>
<style scoped lang="scss">
.dropdown-toggle {
  &.no-arrow {
    &:after {
      display: none;
    }
  }
}

.dropdown-menu {
  max-height: 300px;
  overflow-x: hidden;
  overflow-y: auto;
  content-visibility: auto;
  contain-intrinsic-size: 0 300px;
}
</style>
