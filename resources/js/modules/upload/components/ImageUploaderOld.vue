<script setup lang="ts">
import Crop from "tinycrop";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import { ref, nextTick, computed } from "vue";
import { translate } from "@/lib/metadata";
import { api } from "@/lib/api";
import { chunk } from "@/lib/helper/array";
import { dangerToast } from "@/modules/bootstrap/lib/toast";
import { confirm } from "@/modules/bootstrap/lib/confirm";
import { Fancybox } from "@/lib/fancybox";
import Icon from "@/components/Icon.vue";
import { mdiCrop, mdiDelete, mdiEye, mdiPlusThick } from "@mdi/js";

type Accept = "image/jpeg" | "image/png" | "image/x-png" | "image/webp" | "image/svg+xml";

defineOptions({
  inheritAttrs: false,
})

interface Props {
  section?: string;
  crop?: boolean;
  aspectRatio?: number | false | undefined;
  accept?: Array<Accept> | Accept;
  width?: number | null;
  height?: number | null;
  url?: any | null;
  gallery?: number;
}

const {
  section = "images",
  crop = false,
  aspectRatio = false,
  accept = "image/jpeg",
  width = null,
  height = null,
  url = null,
  gallery = -1,
} = defineProps<Props>();

const cropperModal = ref<any>(null);
const cropperWrapper = ref<any>(null);
const cropper = ref<Crop>(null);
const image = ref<any>(url || null);
const uploading = ref<boolean>(false);
const dragOver = ref<boolean>(false);

const emit = defineEmits(["image", "add", "remove", "done", "view", "cancel"]);

const loadFiles = (e: any) => {
  const files = e.target.files;
  if (files.length > 0) {
    loadFile(files[0]);
  } else {
    emit("cancel");
  }
};

const handleDragOver = (event: DragEvent) => {
  event.preventDefault();
  dragOver.value = true;
};

const handleDragLeave = (event: DragEvent) => {
  event.preventDefault();
  dragOver.value = false;
};

const handleDrop = (event: DragEvent) => {
  event.preventDefault();
  dragOver.value = false;
  const files = event.dataTransfer?.files ?? [];
  if (files.length > 0) {
    loadFile(files[0]);
  } else {
    emit("cancel");
  }
};

const loadFile = (file: File) => {
  emit("add", "file", file);
  const reader = new FileReader();
  reader.onload = (e: any) => {
    const base64 = e.target.result;
    emit("add", "base64", base64);
    if (crop && !["image/svg+xml"].includes(file.type)) {
      cropImage(base64);
    } else {
      imageDataFromBase64(base64).then((imageData: ImageData) => {
        uploadToServer(base64, imageData);
      });
    }
  };
  reader.onerror = () => {
    console.error(`Error occurred reading file: ${file.name}`);
  };
  reader.readAsDataURL(file);
};

const cropImage = (base64: string) => {
  cropperModal.value?.open();
  nextTick(() => {
    setTimeout(() => {
      cropper.value = Crop.create({
        parent: cropperWrapper.value,
        image: base64,
        bounds: {
          width: "100%",
          height: "auto",
        },
        backgroundColors: ["#ffffff", "#f3f3f3"],
        selection: {
          color: "#045892",
          activeColor: "#034b7c",
          aspectRatio: aspectRatio,
          minWidth: width || 200,
          minHeight: height || 300,
          width: width || 400,
          height: height || 500,
          x: 0,
          y: 0,
        },
        onInit: () => {
          console.log("Initial");
        },
      });
      cropper.value
        .on("start", function (region) {
          console.log("Start", region);
        })
        .on("move", function (region) {
          console.log("Move", region);
        })
        .on("resize", function (region) {
          console.log("Resize", region);
        })
        .on("change", function (region) {
          console.log("Change", region);
        })
        .on("end", function (region) {
          console.log("End", region);
        });
    }, 300);
  });
};

const saveCrop = () => {
  if (cropper.value) {
    const c = cropper.value?.selectionLayer?.selection?.region ?? null;
    if (c) {
      const type = cropper.value?.image?.src?.split(";")[0].split("/")[1];
      const canvas: any = document.createElement("canvas");
      canvas.width = c.right - c.left;
      canvas.height = c.bottom - c.top;
      const ctx: any = canvas.getContext("2d");
      ctx.drawImage(cropper.value?.image?.source, -c.left, -c.top);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      cropperModal.value?.close();
      uploadToServer(canvas?.toDataURL(`image/${type}`) ?? null, imageData);
    } else {
      dangerToast("Something went wrong");
    }
  }
};

const imageDataFromBase64 = (base64: string): Promise<ImageData> => {
  return new Promise((resolve) => {
    const canvas: any = document.createElement("canvas");
    const ctx: any = canvas.getContext("2d");
    const image = new Image();
    image.onload = function () {
      ctx.drawImage(image, 0, 0);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      resolve(imageData);
    };
    image.src = base64;
  });
};

const swatches = (imageData: ImageData) => {
  return Object.entries(
    chunk([...imageData.data], 4)
      .map(([r, g, b, a]) => `rgba(${r},${g},${b},${a})`)
      .reduce((colors, color) => {
        if (!Object.hasOwn(colors, color)) {
          colors[color] = 0;
        }
        colors[color]++;
        return colors;
      }, {})
  )
    .filter(([color, count]: any) => count > 1)
    .sort(([, a]: any, [, b]: any) => b - a)
    .slice(0, 10)
    .map(([color]) => color);
};

const uploadToServer = (base64: string, imageData?: ImageData) => {
  if (base64) {
    uploading.value = true;
    image.value = base64;
    api()
      .post("/media/image", { section, base64 })
      .then(({ data }: any) => {
        data = { ...data, palette: imageData ? swatches(imageData) : [] };
        image.value = data;
        emit("image", data);
      })
      .finally(() => {
        uploading.value = false;
        emit("done", base64);
      });
  } else {
    dangerToast("Something went wrong");
  }
};

const closeCropModal = () => {
  cropper.value = null;
  if (cropperWrapper.value) {
    cropperWrapper.value.innerHTML = "";
  }
};

const view = () => {
  if (gallery !== undefined && gallery >= 0) {
    emit("view", gallery);
  } else {
    const fancybox = Fancybox.show([
      {
        src: image.value?.url ?? image.value,
        caption:
          typeof image.value === "object"
            ? `Size: <span class="text-primary">${image.value.size}</span>, Dimension: <span class="text-primary">${image.value.width}x${image.value.height}</span>, Bits: <span class="text-primary">${image.value.bits}</span>, Mime: <span class="text-primary">${image.value.mime}</span>`
            : undefined,
      },
    ]);
    emit("view", fancybox);
  }
};

const remove = () => {
  confirm("Remove Image").then((confirmed: boolean) => {
    if (confirmed) {
      image.value = null;
      emit("image", null);
      emit("remove");
    }
  });
};

const addBtnHeight = computed(() => {
  return aspectRatio ? `${200 / aspectRatio}px` : "100px";
});

const gradient = computed(() => {
  if (
    image.value &&
    Array.isArray(image.value.palette) &&
    image.value.palette.length > 0
  ) {
    const positions = [
      "to top left",
      "to top",
      "to top right",
      "to left",
      "to bottom left",
      "to right",
      "to bottom left",
      "to bottom",
      "to bottom right",
      "to top right",
    ];
    return `${image.value.palette
      .map(
        (color, i) => `linear-gradient(${positions[i]},  transparent, ${color})`
      )
      .join(", ")}`;
  }
  return "#ffffff";
});

defineExpose({ image: () => image.value });
</script>
<template>
  <template v-if="image">
    <div class="image" :class="{ [image.extension]: true }" v-bind="$attrs">
      <img :src="image?.url ?? image" loading="lazy" class="img-fluid" />
      <div v-if="uploading" class="uploading">
        <div
          class="spinner-border text-primary"
          role="status"
          style="width: 3rem; height: 3rem"
        >
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div v-if="!uploading" class="actions">
        <button type="button" class="btn btn-primary btn-sm" @click="view">
          <Icon :path="mdiEye" />
        </button>
        <button type="button" class="btn btn-danger btn-sm" @click="remove">
          <Icon :path="mdiDelete" />
        </button>
      </div>
    </div>
  </template>
  <template v-else>
    <label
      class="media-input-wrapper"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @drop="handleDrop"
      v-bind="$attrs"
    >
      <template v-if="dragOver">
        <div class="drop-zone">Drop an image to upload</div>
      </template>
      <template v-else>
        <input
          type="file"
          @input="loadFiles"
          class="file-input"
          :accept="Array.isArray(accept) ? accept.join(',') : accept"
        />
        <slot name="add">
          <div class="add-btn">
            <Icon :path="mdiPlusThick" />
          </div>
        </slot>
      </template>
    </label>
  </template>
  <Modal
    v-if="crop"
    title="Crop Image"
    ref="cropperModal"
    size="xl"
    @close="closeCropModal"
  >
    <div class="image-crop-wrapper" ref="cropperWrapper"></div>
    <template #buttons>
      <button type="button" class="btn btn-primary" @click="saveCrop">
        <Icon :path="mdiCrop" />&nbsp;{{ translate("Crop") }}
      </button>
    </template>
  </Modal>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";
@import "@scss/mixins/font-size";

.image {
  position: relative;
  width: 200px;
  height: auto;
  border-radius: 10px;
  overflow: hidden;
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  background: v-bind(gradient);
  background-blend-mode: screen;

  &.svg {
    background: $white;
    min-height: 160px;

    img {
      scale: 0.8;
    }
  }

  .uploading,
  .actions {
    position: absolute;
    top: 50%;
    left: 50%;
    translate: -50% -50%;
    width: 100%;
    height: 100%;
    z-index: 2;
    background: rgba(255, 255, 255, 0.6);
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    overflow: hidden;
  }

  .actions {
    gap: 10px;
    transition: all 0.25s ease;
    transform: translateY(100vh);
  }

  &:hover {
    .actions {
      transform: translateY(0);
    }
  }
}

.media-input-wrapper {
  position: relative;
  overflow: hidden;
  display: block;
  width: 200px;

  .file-input {
    position: absolute;
    left: -100vw;
  }

  .add-btn {
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    @include font-size(4);
    border: 1px solid $gray-3;
    background: $white;
    transition: all 0.25s ease;
    border-radius: 10px;
    height: v-bind(addBtnHeight);
    max-width: 200px;
    width: 100%;
    cursor: pointer;
    color: $gray-7;
    transition: all 0.25s ease;

    &:hover {
      color: $blue;
    }
  }

  .drop-zone {
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    text-align: center;
    @include font-size(1.2);
    border: 1px solid $gray-3;
    background: $white;
    transition: all 0.25s ease;
    border-radius: 10px;
    height: v-bind(addBtnHeight);
    max-width: 200px;
    width: 100%;
    cursor: pointer;
    color: $gray-7;
    transition: all 0.25s ease;
    white-space: pre-wrap;
  }
}

.image-crop-wrapper {
  position: relative;
  min-height: 200px;
}
</style>
