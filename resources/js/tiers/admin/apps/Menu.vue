<script setup lang="ts">
import Icon from "@/components/Icon.vue";
import * as icons from "@mdi/js";

type Props = {
  list: Array<
    | [string, string, string, boolean]
    | { title: [string, string]; items: Array<[string, string, boolean]> }
  >;
};
const { list } = defineProps<Props>();

const subHasActive = (items): boolean => {
  return items.filter((item) => item[2]).length > 0;
};
</script>
<template>
  <ul class="menu">
    <template v-for="(item, i) in list" :key="i">
      <li
        :class="{
          'has-sub-menu': !Array.isArray(item),
          active: Array.isArray(item) ? item[3] : subHasActive(item.items),
        }"
      >
        <template v-if="Array.isArray(item)">
          <a :href="item[2]">
            <Icon :path="icons[item[0]]" />
            <span class="title" v-html="item[1]"></span>
          </a>
        </template>
        <template v-else>
          <div class="heading">
            <Icon :path="icons[item.title[0]]" />{{ item.title[1] }}
          </div>
          <ul class="sub-menu">
            <template v-for="(subItem, j) in item.items" :key="j">
              <li :class="{ active: subItem[2] }">
                <a :href="subItem[1]">
                  <span class="title" v-html="subItem[0]"></span>
                </a>
              </li>
            </template>
          </ul>
        </template>
      </li>
    </template>
  </ul>
</template>
<style scoped lang="scss">
@import "@scss/config";
@import "@scss/mixins/font-size";
@import "@scss/mixins/dark";

.menu {
  position: relative;
  padding: 30px 8px;
  margin: 0;
  user-select: none;
  * {
    transition: all 0.25s ease;
  }
  li {
    list-style: none;
    svg {
      scale: 1.2;
    }
    > a {
      text-decoration: none;
      display: flex;
      flex-direction: row;
      gap: 15px;
      align-items: center;
      min-height: 50px;
      padding: 0 7px;
      @include font-size(1.2);
      svg {
        color: $gray-7;
        @include dark {
          color: $gray-2;
        }
      }
      .title {
        color: $gray-7;
        @include dark {
          color: $gray-2;
        }
      }
      &:hover {
        svg {
          color: $orange;
        }
        .title {
          color: $info;
        }
      }
    }
    &.active {
      svg {
        color: $orange;
      }
      > a {
        background: $primary;
        border-radius: 10px;
        .title {
          color: $white;
          @include dark {
            color: $gray-2;
          }
        }
        @include dark {
          background: $info;
        }
      }
    }
    .heading {
      font-weight: bolder;
      color: $primary;
      text-transform: uppercase;
      display: flex;
      flex-direction: row;
      gap: 15px;
      align-items: center;
      min-height: 50px;
      cursor: pointer;
      padding: 0 7px;
      @include font-size(1.4);
      @include dark {
        color: $white;
      }
    }

    .sub-menu {
      margin: 0;
      padding: 0 0 0 20px;
      a {
        height: 40px;
        padding: 0 0 0 20px;
      }
    }
    &.has-sub-menu:not(.active) {
      &:hover {
        .heading {
          svg {
            color: $orange;
          }
        }
      }
    }
  }
}
</style>
