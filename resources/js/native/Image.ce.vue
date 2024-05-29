<script setup lang="ts">
import { rgbaToHex, sortHexColors } from "@/lib/helper/strings";
import { ImageData } from "@/modules/upload/lib/types";
import { computed, ref, watch } from "vue";
import { Fancybox } from "@/lib/fancybox";

type Props = {
  meta: string;
  fancybox?: boolean | string;
};
const props = defineProps<Props>();

const meta: ImageData = JSON.parse(props.meta);
const fancybox = ref<boolean | string | undefined>(props.fancybox);

watch(
  () => props.fancybox,
  (newValue: boolean | string | undefined) => {
    fancybox.value = newValue;
  },
  {
    deep: true,
  }
);

const wrapper = ref<any>(null);

const gradient = computed(() => {
  if (meta?.palette) {
    const palette = sortHexColors(meta?.palette.map(rgbaToHex)).reverse();
    const c: any = [];
    const l = palette.length - 1;
    palette.forEach((color, i) => {
      let per = (i * 100) / l;
      c.push(`${color.hex} ${per}%`);
    });
    return `radial-gradient(farthest-corner at center,${c.join(", ")})`;
  }
  return "#fff";
});

const width = computed(() => {
  if (wrapper.value) {
    const w = parseInt(meta?.width ?? "0");
    if (w < wrapper.value.offsetWidth) {
      return `${w}px`;
    }
    return `${wrapper.value.offsetWidth}px`;
  }
  return "auto";
});

const height = computed(() => {
  if (wrapper.value) {
    const h = parseInt(meta?.height ?? "0");
    const w = parseInt(meta?.width ?? "0");
    if (h > 0 && w > 0) {
      if (w < wrapper.value.offsetWidth) {
        return `${h}px`;
      }
      return `${Math.ceil((wrapper.value.offsetWidth * h) / w)}px`;
    }
  }
  return "auto";
});

const error = () => {
  wrapper.value?.remove?.();
};

const open = () => {
  if (fancybox.value && meta?.url) {
    const options: any = {};
    const data: any = [];
    if (typeof fancybox.value === "string") {
      options.groupAttr = "fancybox";
      options.groupAll = true;
      document
        .querySelectorAll(`[${options.groupAttr}=${fancybox.value}]`)
        .forEach((el: any, i: number) => {
          const { url } = JSON.parse(el.meta);
          data.push({
            src: url,
          });
          if (url === meta?.url) {
            options.startIndex = i;
          }
        });
    } else {
      data.push({
        src: meta?.url,
      });
    }
    Fancybox.show(data, options);
  }
};
</script>
<template>
  <figure
    ref="wrapper"
    class="figure image"
    :class="{ 'with-fancybox': fancybox }"
    :style="{ background: gradient, width, height, margin: 0 }"
    @click="open"
  >
    <img
      :src="meta?.url"
      class="figure-img img-fluid"
      :alt="meta?.size"
      loading="lazy"
      @error="error"
      style="max-width: 100%"
    />
  </figure>
</template>
<style lang="scss">
.figure {
  --color: #fff;
  --bg: rgba(0, 0, 0, 0.7);
  position: relative;
  &.with-fancybox {
    cursor: pointer;
    &:after {
      content: "Click to Enlarge";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      background: var(--bg);
      color: var(--color);
      z-index: -1;
      opacity: 0;
      transition: all 0.25s ease;
    }
  }
  &:hover {
    &:after {
      z-index: 1;
      opacity: 1;
    }
  }
}
</style>
