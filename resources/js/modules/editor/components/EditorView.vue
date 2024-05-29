<script setup lang="ts">
import { computed, onMounted, ref } from "vue";
import { render, Content } from "../core/renderer";

type Props = {
  content: Content;
  rtl?: boolean;
};

const { content, rtl = false } = defineProps<Props>();

const container = ref<any>(null);

const direction = rtl ? "rtl" : "ltr";
const align = rtl ? "right !important" : "left";

const html = computed(() => render(content));

const adjustEmbedIframes = () => {
  const embeds = container.value?.querySelectorAll?.(".block-embed") ?? [];
  embeds.forEach((node: any) => {
    const iframe = node.querySelector("iframe");
    if (iframe) {
      iframe.addEventListener("load", (e: any) => {
        // iframe.style.height = '900px'
        // setTimeout(() => {
        //     // iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px'
        //     iframe.style.height = iframe.contentWindow.document.scrollingElement + 'px'
        // }, 2000)
      });
    }
  });
};

onMounted(() => {
  adjustEmbedIframes();
});
</script>
<template>
  <div class="editor-view-wrapper">
    <div ref="container" v-html="html" class="editor-view"></div>
  </div>
</template>
<style lang="scss">
@import "@scss/bootstrap";

.editor-view-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  .editor-view {
    position: relative;
    padding: 10px;
    background: $white;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    direction: v-bind(direction);
    text-align: v-bind(align);
    @include dark {
      background: $gray-9;
    }

    .tunes {
      position: relative;

      &-call-out {
        padding: 10px;
        border: 1px solid #f44336;
        outline: 5px solid #ffcdd2;
        border-radius: 10px;
      }

      &-citation {
        font-style: italic;
      }

      &-details {
        @include font-size(0.9);
      }
    }

    .block {
      position: relative;
      width: 920px;
      max-width: 100%;

      &-delimiter {
        height: 50px;
      }

      &-header {
        position: relative;

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
          margin: 0;
          padding: 0;
        }
      }

      &-paragraph {
        @include font-size(1);
        color: $gray-7;
        line-height: 25px;
        @include dark {
          color: $gray-2;
        }

        p {
          margin: 0;
          padding: 0;
        }
      }

      &-quote {
        .text {
          @include font-size(1);
          color: $gray-7;
          line-height: 25px;
          padding: 5px 0;
          @include dark {
            color: $gray-2;
          }
        }

        .caption {
          font-weight: bold;
          @include font-size(1);
          color: $gray-7;
          @include dark {
            color: $gray-2;
          }
        }
      }

      &-image {
        position: relative;

        &.image-bg {
          padding: 50px;
          background: #883997;
        }

        &.image-border {
          border: 1px solid $gray-5;
          @include dark {
            color: $gray-3;
          }
        }

        &.image-stretched {
          width: 100%;
          display: flex;
          flex-direction: column;
          align-items: center;
        }

        .figure {
          position: relative;

          img {
            display: block;
            max-width: 100%;
            border-radius: 5px;
          }

          figcaption {
            background: $white;
            color: $gray-8;
            padding: 5px;
            margin-top: 5px;
            border-radius: 5px;
            text-align: center;
            @include dark {
              color: $gray-1;
              background: $gray-9;
            }
          }
        }
      }

      &-linkTool {
        position: relative;

        .link-view {
          position: relative;
          display: flex;
          flex-direction: row;
          width: 100%;
          text-decoration: none;
          gap: 10px;

          .image {
            min-width: 150px;
            aspect-ratio: 1;
            background: var(--image) no-repeat center;
            background-size: 100% auto;
          }

          .info {
            flex: auto;
            display: flex;
            flex-direction: column;
            gap: 5px;

            .title {
              @include font-size(1.2);
              color: $gray-8;
              font-weight: bold;
              margin: 0;
              padding: 0;
              @include dark {
                color: $gray-1;
              }
            }

            .description {
              color: $gray-6;
              @include font-size(0.9);
              margin: 0;
              padding: 0;
              line-height: 22px;
              @include dark {
                color: $gray-2;
              }
            }

            .keywords {
              color: $gray-5;
              @include font-size(0.8);
              @include dark {
                color: $gray-2;
              }
            }

            .site {
              color: $primary;
              font-weight: bold;
              @include font-size(1);
              @include dark {
                color: $info;
              }
            }
          }
        }
      }

      &-embed {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 10px;

        iframe {
          max-width: 100%;
          height: 100%;
        }

        .caption {
          background: $white;
          color: $gray-8;
          padding: 5px;
          border-radius: 5px;
          text-align: center;
          @include dark {
            color: $gray-1;
            background: $black;
          }
        }
      }
    }
  }
}
</style>
