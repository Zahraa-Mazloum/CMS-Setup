<script setup lang="ts">
import { ref, nextTick } from "vue";
import { vClickOutside } from "@/directives/click-outside";

const active = ref<boolean>(false);
const contextMenu = ref<any>(null);
const element = ref<any>(null);
const x = ref<number | string>(0);
const y = ref<number | string>(0);

const emit = defineEmits(["open", "close"]);

const open = (event) => {
  active.value = true;
  document.body.click();
  nextTick(() => {
    calculatePosition(event.clientX, event.clientY);
    emit("open");
  });
};

const close = () => {
  active.value = false;
  emit("close");
};

const calculatePosition = (clientX: number, clientY: number) => {
  x.value = clientX;
  y.value = clientY;
  const vw = Math.max(
    document.documentElement.clientWidth || 0,
    window.innerWidth || 0
  );
  const vh = Math.max(
    document.documentElement.clientHeight || 0,
    window.innerHeight || 0
  );
  if (contextMenu.value) {
    const { width, height } = contextMenu.value.getBoundingClientRect();
    if (width + x.value > vw && x.value - width < 0) {
      x.value = Math.ceil((width + x.value) / 2);
    } else if (width + x.value > vw) {
      x.value -= width;
    }
    if (height + y.value > vh && y.value - height < 0) {
      y.value = Math.ceil((height + y.value) / 2);
    } else if (height + y.value > vh) {
      y.value -= height;
    }
  }
  x.value = `${x.value}px`;
  y.value = `${y.value}px`;
};

defineExpose({ open, close });
</script>
<template>
  <slot name="default" ref="element" :open="(event) => open(event)"></slot>
  <Teleport to="body">
    <div
      v-if="active"
      ref="contextMenu"
      class="context-menu"
      v-click-outside="close"
    >
      <slot name="contextmenu">
        <div class="fallback">No content found</div>
      </slot>
    </div>
  </Teleport>
</template>
<style scoped lang="scss">
@import "@scss/config";
@import "@scss/mixins/dark";

.context-menu {
  position: absolute;
  top: v-bind(y);
  left: v-bind(x);
  display: block;
  padding: 10px;
  background: $white;
  border: 1px solid $gray-2;
  min-width: 200px;
  width: fit-content;
  border-radius: 10px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16);
  white-space: nowrap;
  z-index: 100000;
  user-select: none;
  @include dark {
    background: $gray-9;
    border: 1px solid $gray-7;
  }

  .fallback {
    position: relative;
    color: $gray-6;
    user-select: none;
    @include dark {
      color: $gray-3;
    }
  }
}
</style>
