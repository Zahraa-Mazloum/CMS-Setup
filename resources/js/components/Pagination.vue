<script setup lang="ts">
import { pagination } from "@/lib/helper/functions";
import { translate } from "@/lib/metadata";
import { computed, ref, watch } from "vue";
import Icon from "@/components/Icon.vue";
import {
  mdiChevronDoubleLeft,
  mdiChevronDoubleRight,
  mdiChevronLeft,
  mdiChevronRight,
} from "@mdi/js";
import Dropdown from "@/modules/bootstrap/components/Dropdown.vue";
import DropdownItem from "@/modules/bootstrap/components/DropdownItem.vue";

type Props = {
  page?: number;
  perPage?: number;
  total?: number;
  pages?: number;
  buttons?: number;
  sizes?: Array<number>;
};

const {
  page = 1,
  perPage = 10,
  total = 0,
  buttons = 5,
  sizes = [5, 10, 25, 50],
} = defineProps<Props>();

type Emits = {
  "update:page": any;
  "update:perPage": any;
  "update:total": any;
  "update:buttons": any;
  change: any;
};
const emit = defineEmits<Emits>();

const currentPage = ref(page);
const customPage = ref(page);
const pageSize = ref(perPage);
const pagesNb = computed(() => Math.ceil(total / pageSize.value));
const items = computed(() =>
  pagination(currentPage.value, perPage, total, buttons)
);
const dropDown: any = ref(null);

watch(
  [currentPage, pageSize],
  () => {
    emit("update:page", currentPage);
    emit("update:perPage", pageSize);
    emit("update:total", total);
    emit("update:buttons", buttons);
    emit("change", {
      page: currentPage,
      perPage: pageSize,
      total: total,
      pages: pagesNb.value,
      buttons: buttons,
    });
  },
  { deep: true }
);

watch(pageSize, () => {
  currentPage.value = 1;
});

const goTo = (page) => {
  currentPage.value = page;
};
const increment = () => {
  customPage.value++;
  if (customPage.value > pagesNb.value) {
    customPage.value = 1;
  }
};
const decrement = () => {
  customPage.value--;
  if (customPage.value < 1) {
    customPage.value = pagesNb.value;
  }
};
const changeSize = (size) => {
  pageSize.value = size;
  dropDown.value.close();
};

defineExpose({ goTo, increment, decrement, changeSize });
</script>
<template>
  <div class="pagination-component">
    <div class="count" v-if="total > 0">
      {{
        translate(
          ":?-:? of :? rows",
          perPage * currentPage - (perPage - 1),
          perPage * currentPage <= total ? perPage * currentPage : total,
          total
        )
      }}
    </div>
    <nav aria-label="Page navigation" v-if="items.length > 1" class="pages">
      <ul class="pagination pagination-sm">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a
            class="page-link"
            href="#"
            tabindex="-1"
            aria-disabled="true"
            @click.prevent="goTo(1)"
          >
            <Icon :path="mdiChevronDoubleLeft" />
          </a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <a
            class="page-link"
            href="#"
            tabindex="-1"
            aria-disabled="true"
            @click.prevent="goTo(currentPage - 1)"
          >
            <Icon :path="mdiChevronLeft" />
          </a>
        </li>
        <li
          class="page-item"
          v-for="p in items"
          :key="p"
          :class="{ active: p === currentPage }"
        >
          <span class="page-link" v-if="p === currentPage">{{ p }}</span>
          <a v-else class="page-link" href="#" @click.prevent="goTo(p)">{{
            p
          }}</a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === pagesNb }">
          <a class="page-link" href="#" @click.prevent="goTo(currentPage + 1)">
            <Icon :path="mdiChevronRight" />
          </a>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === pagesNb }">
          <a
            class="page-link"
            href="#"
            tabindex="-1"
            aria-disabled="true"
            @click.prevent="goTo(pagesNb)"
          >
            <Icon :path="mdiChevronDoubleRight" />
          </a>
        </li>
      </ul>
    </nav>
    <div class="per-page">
      <Dropdown
        v-if="total > 0"
        ref="dropDown"
        :title="`${perPage} ${translate('rows')}`"
        auto-close
      >
        <template v-for="(size, i) in sizes" :key="size">
          <DropdownItem
            v-if="i === 0 || (sizes.at(i - 1) ?? 0) <= total"
            @click.prevent="changeSize(size)"
            >{{ size }} {{ translate("rows") }}</DropdownItem
          >
        </template>
      </Dropdown>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/config";
@import "@scss/mixins/font-size";
@import "@scss/mixins/dark";

.pagination-component {
  position: relative;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  gap: 30px;
  min-width: 100%;
  height: 40px;

  .count {
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: center;
    color: $gray-6;
    @include font-size(0.9);
  }
  .pages {
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: center;
    .pagination {
      margin: 0;
      .page-item {
        cursor: pointer;
        .page-link {
          @include font-size(1.2);
          height: 40px;
          min-width: 40px;
          display: flex;
          flex-direction: row;
          align-items: center;
          justify-content: center;
        }
        &.active {
          .page-link {
            @include dark {
              background-color: $info;
              border-color: darken($info, 10%);
            }
          }
        }
        &.disabled {
          cursor: not-allowed;
        }
      }
    }
  }
  .per-page {
    position: relative;
    display: flex;
    flex-direction: row;
    align-items: center;
  }
}
</style>
