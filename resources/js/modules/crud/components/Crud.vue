<script setup lang="ts" generic="T extends {} = {}, S extends string = string">
import Icon from "@/components/Icon.vue";
import { Fields, FetchCallback, Field } from "@/modules/crud/lib/crud";
import { isDark, useThemeStore } from "@/modules/theme";
import {
  mdiDotsVertical,
  mdiMinusBoxOutline,
  mdiPlusBoxOutline,
  mdiRefresh,
  mdiSort,
  mdiSortAscending,
  mdiSortDescending,
} from "@mdi/js";
import { shallowRef, ref, computed, getCurrentInstance, onMounted, nextTick } from 'vue';
import { storeToRefs } from "pinia";
import Pagination from "@/components/Pagination.vue";
import Search from "@/components/Search.vue";
import Dropdown from "@/modules/bootstrap/components/Dropdown.vue";
import Button from "@/modules/bootstrap/components/Button.vue";

type Props = {
  fields: Fields<S>;
  fetch: FetchCallback<T>;
  keyName?: string;
  theme?: false | 'light' | 'dark';
  orderBy?: false | string;
};

const { fields, fetch, keyName = "id", theme = false, orderBy = false } = defineProps<Props>();

const columns = computed<Fields<S>>(() => fields);

type GlobalSlots = {
  start: any;
  end: any;
  before: any;
  after: any;
  startSearch: any;
  beforeSearch: any;
  afterSearch: any;
  endSearch: any;
  topActions: any;
  actions: (props: { item: T }) => any;
  moreActions: (props: { item: T }) => any;
  details: (props: { item: T }) => any;
};

type HeaderSlots = {
  [Property in S as `${Property}Header`]: (props: {
    field: Field<Property>;
  }) => any;
};

type ValueSlots = {
  [Property in S as `${Property}Value`]: (props: {
    field: Field<Property>;
    item: T;
  }) => any;
};

type Slots = GlobalSlots & HeaderSlots & ValueSlots;
defineSlots<Slots>();

type Emits = {
  empty: any;
  noEmpty: any;
  openDetails: [item: T];
  closeDetails: [item: T];
};
const emit = defineEmits<Emits>();

const instance = getCurrentInstance();

const themeStore = useThemeStore();
const { isDarkTheme } = storeToRefs<any>(themeStore);

const data = shallowRef<T[]>([]);
const page = ref<number>(1);
const perPage = ref<number>(5);
const keyword = ref<any>("");
const sortName = ref<string>((orderBy || keyName).replace('-', ''));
const sortAsc = ref<boolean>(orderBy ? orderBy.startsWith('-') ? false : true : true);
const total = ref(0);
const pages = ref(0);
const pagesButtons = ref(5);
const details = ref<any>({});
const refresher = ref<any>(null);
const version = ref<number>(0);

const load = async (callback: (params: {data: Array<T>, total: number, page: number, perPage: number, pages: number}) => void = () => { }) => {
  refresher.value?.start?.();
  try {
    const response = await fetch({
      page: page.value,
      perPage: perPage.value,
      keyword: keyword.value,
      sort: `${sortAsc.value ? "" : "-"}${sortName.value}`.trim(),
      fields: columns.value,
    });
    data.value = response.data;
    total.value = response?.total ?? 0;
    page.value = response?.page ?? 1;
    perPage.value = response?.perPage ?? 1;
    pages.value = response?.pages ?? 0;
    details.value = {};
    if (data.value.length > 0) {
      emit("noEmpty");
    } else {
      emit("empty");
    }
    version.value++;
    nextTick(() => {
      callback({data: data.value, total: total.value, page: page.value, perPage: perPage.value, pages: pages.value});
    })
  } catch (e: any) { }
  refresher.value?.stop?.();
};

const loadSort = async (field: Field<S>) => {
  if (field.sortable) {
    sortName.value = field.name;
    sortAsc.value = !sortAsc.value;
    load();
  }
};

const changePage = (pagination) => {
  page.value = pagination.page.value;
  perPage.value = pagination.perPage.value;
  load();
};

const search = () => {
  page.value = 1;
  load();
};
const clear = () => {
  page.value = 1;
  keyword.value = "";
  load();
};

const refresh = (callback: (params: {data: Array<T>, total: number, page: number, perPage: number, pages: number}) => void = () => { }) => {
  load(callback);
};

const isVisible = (field: Field) => {
  return field.visible === undefined || field.visible;
};

const colsCount = computed(() => {
  return (
    fields.filter((field) => isVisible(field)).length +
    1 /* countable rows # */ +
    (instance?.proxy?.$slots?.actions ? 1 : 0) +
    (instance?.proxy?.$slots?.moreActions ? 1 : 0) +
    (instance?.proxy?.$slots?.details ? 1 : 0)
  );
});

const toggleDetails = (item: T) => {
  const opened = !inDetailsMode(item);
  details.value = { ...details.value, [item[keyName]]: opened };
  if (opened) {
    emit('openDetails', item);
  } else {
    emit('closeDetails', item);
  }
};

const inDetailsMode = (item: T) => {
  return (
    Object.hasOwn(details.value, item[keyName]) && details.value[item[keyName]]
  );
};

const length = computed(() => data.value?.length ?? 0)

onMounted(() => {
  load();
});

defineExpose({ refresh, length: () => length.value, data: () => data.value, toggleDetails });
</script>
<template>
  <div class="crud-component" :key="`crud-${version}`">
    <slot name="start"></slot>
    <div class="row">
      <div class="col-md-5">
        <div class="d-flex flex-horizontal gap-3">
          <Button color="primary" @click="refresh" ref="refresher" spinner-small spinner-color="light"
            style="min-width: 50px">
            <Icon :path="mdiRefresh" />
          </Button>
          <Search v-model="keyword" @clear="clear" @search="search">
            <template v-if="$slots['startSearch']" #start-search>
              <slot name="startSearch"> </slot>
            </template>
            <template v-if="$slots['beforeSearch']" #before-search>
              <slot name="beforeSearch"> </slot>
            </template>
            <template v-if="$slots['aftersearch']" #after-search>
              <slot name="afterSearch"> </slot>
            </template>
            <template v-if="$slots['endSearch']" #end-search>
              <slot name="endSearch"> </slot>
            </template>
          </Search>
        </div>
      </div>
      <div class="col-md-7">
        <div class="d-flex gap-2 justify-content-end align-items-center h-100">
          <slot name="topActions"></slot>
        </div>
      </div>
    </div>
    <slot name="before"></slot>
    <div class="table-responsive">
      <table class="table table-bordered table-striped table-hover m-0"
        :class="{ 'table-dark': (!theme && isDarkTheme) || (theme && theme === 'dark') }">
        <thead>
          <tr>
            <th v-if="$slots.details" class="fitable no-select">&nbsp;</th>
            <th class="fitable no-select">#</th>
            <template v-for="(field, index) in columns" :key="`${field.name}-${index}`">
              <th v-if="isVisible(field)" :class="{
                fitable: field.fitable,
                'no-select': true,
                pointer: field.sortable,
                'table-active': field.sortable && sortName === field.name,
              }">
                <div class="d-flex gap-2" @click.prevent.stop="loadSort(field)">
                  <span>
                    <slot :name="`${field.name}Header`" :field="field">
                      {{ field.label }}
                    </slot>
                  </span>
                  <template v-if="field.sortable && sortName === field.name">
                    <Icon :path="sortAsc ? mdiSortAscending : mdiSortDescending" :class="{ 'ms-auto': true }"
                      color="primary" />
                  </template>
                  <template v-else>
                    <Icon :path="mdiSort" class="ms-auto text-muted" />
                  </template>
                </div>
              </th>
            </template>
            <template v-if="$slots.moreActions || $slots.actions">
              <th :colspan="$slots.moreActions && $slots.actions ? 2 : 1" class="fitable no-select">
                Actions
              </th>
            </template>
          </tr>
        </thead>
        <tbody>
          <template v-for="(item, r) in data" :key="item[keyName]">
            <tr>
              <td v-if="$slots.details" class="fitable no-select align-middle">
                <Icon :path="inDetailsMode(item) ? mdiMinusBoxOutline : mdiPlusBoxOutline
                  " @click="toggleDetails(item)" class="pointer" />
              </td>
              <td class="fitable no-select align-middle">{{ r + 1 }}</td>
              <template v-for="(field, c) in columns" :key="`${field.name}-${c}`">
                <td v-if="isVisible(field)" :class="{
                  fitable: field.fitable,
                  'select-all': true,
                  'table-active': field.sortable && sortName === field.name,
                  border: field.sortable && sortName === field.name,
                  'align-middle': true,
                }">
                  <slot :name="`${field.name}Value`" :field="field" :item="item">
                    {{ item[field.name as string] }}
                  </slot>
                </td>
              </template>
              <template v-if="$slots.actions">
                <td class="fitable no-select align-middle">
                  <div class="d-flex gap-2 align-items-start">
                    <slot name="actions" :item="item"></slot>
                  </div>
                </td>
              </template>
              <template v-if="$slots.moreActions">
                <td class="fitable no-select align-middle">
                  <Dropdown no-arrow div>
                    <template #button>
                      <Icon :path="mdiDotsVertical" />
                    </template>
                    <slot name="moreActions" :item="item"></slot>
                  </Dropdown>
                </td>
              </template>
            </tr>
            <template v-if="$slots.details && inDetailsMode(item)">
              <tr>
                <td :colspan="colsCount" class="p-0 align-middle">
                  <slot name="details" :item="item"></slot>
                </td>
              </tr>
            </template>
          </template>
        </tbody>
      </table>
    </div>
    <slot name="after"></slot>
    <Pagination v-model:page="page" v-model:per-page="perPage" v-model:total="total" v-model:buttons="pagesButtons"
      @change="changePage">
    </Pagination>
    <slot name="end"></slot>
  </div>
</template>
<style scoped lang="scss">
.crud-component {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.fitable {
  text-align: center;
  white-space: nowrap;
  width: 1%;
}

.no-select {
  user-select: none;
}

.select-all {
  user-select: all;
}

.pointer {
  cursor: pointer;
}
</style>
