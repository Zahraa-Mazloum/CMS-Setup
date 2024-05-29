<script lang="ts" setup>
import { Field } from "@/modules/form/lib/field";
import FieldWrapper, {
  GroupSlots,
} from "@/modules/form/components/base/FieldWrapper.vue";
import { onMounted, onUnmounted, ref, computed, nextTick } from "vue";
import EditorJS from "@editorjs/editorjs";
import Modal from "@/modules/bootstrap/components/Modal.vue";
import EditorView from "@/modules/editor/components/EditorView.vue";
import tools from "@/modules/editor/blocks";
import { mdiEye, mdiFullscreen, mdiFullscreenExit } from "@mdi/js";
import Icon from "@/components/Icon.vue";

defineOptions({
  inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
  field: Field;
  noLabel?: boolean;
  rtl?: boolean;
  altitude?: number;
};
const props = defineProps<Props>();

const { field, noLabel = false, rtl = false, altitude = 400 } = props;

const container = ref<any>(null);
const element = ref<any>(null);
const previewModal = ref<any>(null);
const editor = ref<EditorJS | null>(null);
const fullscreen = ref<boolean>(false);
const content = ref<any>(null);

const height = computed(() => `${altitude}px`);

const preview = () => {
  content.value = field.value;
  nextTick(() => {
    previewModal.value?.open();
  });
};

const previewClose = () => {
  content.value = null;
};

const toggleFullscreen = () => {
  fullscreen.value = !fullscreen.value;
};

onMounted(() => {
  editor.value = new EditorJS({
    holder: element.value,
    tools,
    data: field.value,
    i18n: {
      direction: rtl ? "rtl" : "ltr",
    },
    tunes: ["textVariant"],
    onChange: (api, event) => {
      editor.value
        ?.save()
        ?.then((outputData) => {
          field.update(outputData);
        })
        ?.catch((error) => {
          console.log("Saving failed: ", error);
        });
    },
  });
});

onUnmounted(() => {
  editor.value?.destroy();
});
</script>
<template>
  <FieldWrapper :field="field" :no-label="noLabel">
    <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
      <slot :name="slot" v-bind="{ ...scope }" />
    </template>
    <template #="{ id }">
      <div
        class="editor-wrapper"
        :class="{ fullscreen }"
        v-bind="$attrs"
        ref="container"
      >
        <div class="editor-js" ref="element"></div>
        <div class="actions">
          <a href="#" @click.prevent="preview">
            <Icon :path="mdiEye" />
          </a>
          <a href="#" @click.prevent="toggleFullscreen">
            <Icon :path="fullscreen ? mdiFullscreenExit : mdiFullscreen" />
          </a>
        </div>
      </div>
    </template>
  </FieldWrapper>
  <Modal
    v-if="content && content.blocks?.length > 0"
    ref="previewModal"
    title="Preview"
    @close="previewClose"
    size="xl"
  >
    <EditorView :content="content" :rtl="rtl" />
  </Modal>
</template>
<style lang="scss">
@import "@scss/bootstrap";

.editor-wrapper {
  position: relative;
  border: 1px solid $gray-2;
  border-radius: 5px;
  height: v-bind(height);
  overflow-y: auto;
  overflow-x: hidden;
  background: $white;
  display: flex;
  flex-direction: row;
  padding: 10px;
  width: 100%;
  @include dark {
    background: $gray-9;
    border-color: $gray-7;
  }

  .editor-js {
    position: relative;
    z-index: 0;
    flex: auto;
  }

  .actions {
    position: sticky;
    top: 0;
    width: 40px;
    min-width: 40px;
    display: flex;
    flex-direction: column;
    gap: 5px;

    > a {
      text-decoration: none;
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 40px;
      @include font-size(1.7);
      color: $white;
      background: $black;
      transition: all 0.25s ease;
      border-radius: 50%;

      @include dark {
        color: $black;
        background: $white;
      }

      &:hover {
        color: $primary;
      }
    }
  }

  &.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
  }

  .codex-editor {
    .ce-toolbar__plus,
    .ce-toolbar__settings-btn {
      @include dark {
        color: $white !important;
      }
      &:hover {
        @include dark {
          color: $black !important;
        }
      }
    }
    &.codex-editor--rtl {
      &.codex-editor--narrow {
        .codex-editor__redactor {
          .ce-block {
            .ce-block__content {
              direction: rtl !important;
            }
          }
        }
        .ce-toolbar__actions {
          right: -86px !important;
          left: auto !important;

          .ce-popover {
            direction: ltr !important;
          }
        }
      }
      .tc-add-column {
        z-index: 10 !important;
      }
    }
  }
}
</style>
