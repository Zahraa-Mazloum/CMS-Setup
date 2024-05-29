<script setup lang="ts">
import { api } from "@/lib/api";
import { isDark } from "@/modules/theme";
import { ref } from "vue";

type NumberItem = {
  name: string;
  count: number;
};

const numbers = ref<null | Array<NumberItem>>(null);

const fetchNumbers = () => {
  api()
    .get("/statistic")
    .then(({ data }: any) => {
      numbers.value = JSON.parse(data.numbers) as Array<NumberItem>;
    });
};

fetchNumbers();
</script>
<template>
  <div :class="{ dashboard: true, dark: isDark() }">
    <div v-if="numbers" class="numbers">
      <template v-for="item in numbers" :key="item.name">
        <div class="item" :data-name="item.name">
          <div class="name">{{ item.name }}</div>
          <div class="count">{{ item.count }}</div>
        </div>
      </template>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";
@import "@scss/mixins/font-size";
@import "@scss/functions/flex-gap-width";

.dashboard {
  position: relative;

  .numbers {
    $gap: 20px;
    $columns: 5;
    position: relative;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: $gap;

    .item {
      position: relative;
      display: flex;
      flex-direction: row;
      gap: 15px;
      min-width: flexGapWidth($columns, $gap);
      padding: 10px 20px;
      background: rgba($black, 0.16);
      border: 1px solid rgba($black, 0.35);
      user-select: none;
      border-radius: 20px;
      align-items: center;
      overflow: hidden;
      .name {
        @include font-size(1.5);
        text-transform: uppercase;
      }
      .count {
        @include font-size(4);
        margin-left: auto;
        -webkit-text-stroke: 2px $black;
        color: transparent;
        font-weight: bold;
      }
      &:before {
        content: attr(data-name);
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: auto;
        min-width: 100%;
        height: 100%;
        @include font-size(5);
        white-space: nowrap;
        font-weight: bold;
        -webkit-text-stroke: 2px rgba($black, 0.04);
        color: transparent;
      }
    }
  }

  &.dark {
    .numbers {
      .item {
        background: rgba($white, 0.16);
        border: 1px solid rgba($white, 0.35);
        .count {
          -webkit-text-stroke: 2px $white;
        }
        &:before {
          -webkit-text-stroke: 2px rgba($white, 0.04);
        }
      }
    }
  }
}
</style>
