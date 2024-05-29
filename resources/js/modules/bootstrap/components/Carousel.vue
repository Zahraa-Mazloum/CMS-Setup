<script setup lang="ts" generic="T extends any = any">
import { autoId } from "@/lib/helper/strings";
import { isDark } from "@/modules/theme";
import { Carousel } from "bootstrap";
import { ref, onMounted, onUnmounted } from "vue";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  list: T[];
  interval?: number;
  keyborad?: boolean;
  pause?: "hover" | false;
  ride?: "carousel" | boolean;
  touch?: boolean;
  wrap?: boolean;
  start?: number;
  fade?: boolean;
  indicators?: boolean;
  controls?: boolean;
};
const {
  list,
  interval = 5000,
  keyborad = false,
  pause = false,
  ride = false,
  touch = false,
  wrap = false,
  start = 0,
  fade = false,
  indicators = false,
  controls = false,
} = defineProps<Props>();

type Slots = {
  slide: (props: { item: T; index: number }) => void;
  caption: (props: { item: T; index: number }) => void;
};
defineSlots<Slots>();

type Emits = {
  sliding: [
    direction: "left" | "right",
    from: number,
    to: number,
    relatedTarget: any
  ];
  slide: [
    direction: "left" | "right",
    from: number,
    to: number,
    relatedTarget: any
  ];
};
const emit = defineEmits<Emits>();

const id = autoId("carousel");

const element = ref<any>(null);
const carousel = ref<any>(null);
const active = ref<number>(start);

const onSliding = (event) => {
  emit("sliding", event.direction, event.from, event.to, event.relatedTarget);
};

const onSlide = (event) => {
  let to = event.to;
  if (to < 0) {
    to = list.length - 1;
  } else if (to >= list.length) {
    to = 0;
  }
  active.value = to;
  emit("slide", event.direction, event.from, event.to, event.relatedTarget);
};

const create = () => {
  carousel.value = new Carousel(element.value, {
    interval,
    keyborad,
    pause,
    ride,
    touch,
    wrap,
  });
  element.value.addEventListener("slide.bs.carousel", onSliding);
  element.value.addEventListener("slid.bs.carousel", onSlide);
};

const next = () => {
  carousel.value?.next?.();
};

const prev = () => {
  carousel.value?.prev?.();
};

const to = (index: number) => {
  carousel.value?.to?.(index);
};

const doPause = () => {
  carousel.value?.pause?.();
};

const nextWhenVisible = () => {
  carousel.value?.nextWhenVisible?.();
};

const cycle = () => {
  carousel.value?.cycle?.();
};

const dispose = () => {
  carousel.value?.dispose();
  element.value.removeEventListener("slide.bs.carousel", onSliding);
  element.value.removeEventListener("slid.bs.carousel", onSlide);
};

defineExpose({ next, prev, to, pause: doPause, nextWhenVisible, cycle });

onMounted(() => {
  create();
});

onUnmounted(() => {
  dispose();
});
</script>
<template>
  <div
    :id="id"
    ref="element"
    class="carousel slide"
    :class="{ 'carousel-fade': fade, 'carousel-dark': isDark() }"
  >
    <div v-if="indicators" class="carousel-indicators">
      <template v-for="(item, i) in list" :key="i">
        <button
          type="button"
          :class="{ active: active === i }"
          aria-current="true"
          :aria-label="`Slide ${i + 1}`"
          @click="to(i)"
          :data-bs-target="`#${id}`"
        ></button>
      </template>
    </div>
    <div class="carousel-inner">
      <template v-for="(item, i) in list" :key="i">
        <div class="carousel-item" :class="{ active: i === start }">
          <slot name="slide" :item="item" :index="i"></slot>
          <template v-if="$slots.caption">
            <div class="carousel-caption d-none d-md-block">
              <slot name="caption" :item="item" :index="i"></slot>
            </div>
          </template>
        </div>
      </template>
    </div>
    <button
      v-if="controls"
      class="carousel-control-prev"
      type="button"
      @click="prev"
      :data-bs-target="`#${id}`"
    >
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button
      v-if="controls"
      class="carousel-control-next"
      type="button"
      @click="next"
      :data-bs-target="`#${id}`"
    >
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</template>
