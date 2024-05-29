<script setup lang="ts">
import { chunk } from "@/lib/helper/array";
import { computed, ref, watch } from "vue";
import { calculateDays, getDateFormat } from "../lib/calendar";
import { mdiChevronLeft, mdiChevronRight } from "@mdi/js";
import { isDark } from "@/modules/theme";
import Icon from "@/components/Icon.vue";

const props = defineProps({
  date: {
    type: [String, Object as () => Date],
    default: () => new Date(),
  },
});

const emit = defineEmits(["change", "done"]);

let date = ref(
  props.date
    ? props.date instanceof Date
      ? props.date
      : new Date(props.date)
    : new Date()
);
const day = ref(date.value.getDate());
const month = ref(date.value.getMonth());
const year = ref(date.value.getFullYear());
const view = ref("dates");
const centerYear = ref(year.value);

watch(
  date,
  (d) => {
    emit("change", getDateFormat(d as Date));
  },
  {
    immediate: true,
  }
);

watch(year, (value) => {
  centerYear.value = value;
});

const setView = (name) => {
  view.value = name;
};

const setYear = (y) => {
  year.value = y;
  date.value = new Date(year.value, month.value, day.value);
  setView("months");
};

const setMonth = (m, y) => {
  month.value = m;
  year.value = y;
  date.value = new Date(year.value, month.value, day.value);
  setView("dates");
};

const setDay = (ds) => {
  date.value = new Date(ds);
  day.value = date.value.getDate();
  month.value = date.value.getMonth();
  year.value = date.value.getFullYear();
  emit("done");
};

const yearsMeta = computed(() => {
  return {
    from: centerYear.value - 7,
    to: centerYear.value + 7,
  };
});

const years = computed(() => {
  const y: any = [];
  for (let i = yearsMeta.value.from; i <= yearsMeta.value.to; i++) {
    y.push(i);
  }
  return chunk(y, 3);
});

const monthsNames = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];

const months = computed(() => {
  const m = monthsNames.map((v, i) => {
    return {
      value: i,
      name: v,
    };
  });
  return chunk(m, 3);
});

const previous = () => {
  if (view.value === "years") {
    centerYear.value -= 15;
  } else if (view.value === "months") {
    year.value--;
  } else if (view.value === "dates") {
    month.value--;
    if (month.value < 0) {
      month.value = 11;
      year.value--;
    }
  }
  date.value = new Date(year.value, month.value, day.value);
};
const next = () => {
  if (view.value === "years") {
    centerYear.value += 15;
  } else if (view.value === "months") {
    year.value++;
  } else if (view.value === "dates") {
    month.value++;
    if (month.value > 11) {
      month.value = 0;
      year.value++;
    }
  }
  date.value = new Date(year.value, month.value, day.value);
};

const daysNames = ["Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"];

const days = computed(() => {
  return chunk(calculateDays(month.value, year.value, date.value), 7);
});
</script>
<template>
  <div class="calendar-component">
    <div class="card">
      <div class="card-header">
        <div class="row">
          <div class="col text-center">
            <Icon :path="mdiChevronLeft" @click.stop.prevent="previous()" />
          </div>
          <div class="col-sm-8">
            <div class="heading">
              <template v-if="view === 'years'">
                <strong
                  :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                  >{{ yearsMeta.from }}</strong
                >
                -
                <strong
                  :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                  >{{ yearsMeta.to }}</strong
                >
              </template>
              <template v-if="view === 'months'">
                <strong
                  :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                  @click.stop.prevent="setView('years')"
                  >{{ year }}</strong
                >
              </template>
              <template v-if="view === 'dates'">
                <div @click.stop.prevent="setView('months')">
                  <strong
                    :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                    >{{ date.getDate().toString().padStart(2, "0") }}</strong
                  >
                  &nbsp;
                  <strong
                    :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                    >{{ monthsNames[date.getMonth()] }}</strong
                  >
                  &nbsp;
                  <strong
                    :class="{ 'text-dark': !isDark(), 'text-light': isDark() }"
                    >{{ date.getFullYear() }}</strong
                  >
                </div>
              </template>
            </div>
          </div>
          <div class="col text-center">
            <Icon :path="mdiChevronRight" @click.stop.prevent="next()" />
          </div>
        </div>
      </div>
      <div class="card-body">
        <div
          class="view-layout view-years"
          :class="{ active: view === 'years' }"
        >
          <div v-for="(items, i) in years" :key="i" class="row no-full-height">
            <div v-for="y in items" :key="y" class="col">
              <a
                href="#"
                class="item year"
                :class="{ active: y === year }"
                @click.stop.prevent="setYear(y)"
                >{{ y }}</a
              >
            </div>
          </div>
        </div>
        <div
          class="view-layout view-months"
          :class="{ active: view === 'months' }"
        >
          <div v-for="(items, i) in months" :key="i" class="row no-full-height">
            <div v-for="m in items" :key="m" class="col">
              <a
                href="#"
                class="item month"
                :class="{ active: m.value === month }"
                @click.stop.prevent="setMonth(m.value, year)"
                >{{ m.name }}</a
              >
            </div>
          </div>
        </div>
        <div
          class="view-layout view-dates"
          :class="{ active: view === 'dates' }"
        >
          <div class="row">
            <div v-for="d in daysNames" :key="d" class="col px-0">
              <div class="day-name">{{ d }}</div>
            </div>
          </div>
          <div v-for="(chunk, i) in days" :key="i" class="row">
            <div v-for="day in chunk" :key="day.date" class="col px-0">
              <div
                class="day-date"
                @click.stop.prevent="setDay(day.date)"
                :class="{
                  active: day.active,
                  today: day.today,
                  [day.type]: true,
                }"
              >
                {{ day.text }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";

.calendar-component {
  position: relative;

  .card {
    * {
      user-select: none;
    }

    --bs-card-bg: #{$white};
    @include dark {
      --bs-card-bg: #{$gray-9};
    }

    border-radius: 0;
    border: none;

    .card-header {
      border-radius: 0;
      svg {
        cursor: pointer;
        color: $gray-9;
        @include dark {
          color: $gray-1;
        }
      }

      .heading {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        min-height: 100%;
        cursor: pointer;
      }
    }

    .card-body {
      position: relative;
      padding-top: 260px;
      overflow: hidden;
      border-radius: 0;

      .view-layout {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        opacity: 0;
        visibility: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: $white;
        @include dark {
          background: $gray-9;
        }

        &.active {
          z-index: 1;
          opacity: 1;
          visibility: visible;
        }

        .row {
          min-width: 100%;
          margin-bottom: 10px;

          &:last-child {
            margin: 0;
          }

          .item {
            position: relative;
            text-decoration: none;
            color: dark;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 40px;
            padding: 0 10px;
            cursor: pointer;
            border-radius: 10px;

            &:hover,
            &.active {
              background: $gray-2;
              color: $black;
              @include dark {
                background: $gray-8;
                color: $white;
              }
            }
          }

          .day-name {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 40px;
            padding: 0 10px;
            color: $black;
            border-bottom: 1px solid $gray-2;
            @include dark {
              color: $white;
              border-bottom: 1px solid $gray-8;
            }
          }

          .day-date {
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 36px;
            padding: 0 10px;
            margin: 2px;
            cursor: pointer;
            border-radius: 10px;
            &,
            * {
              transition: all 0.25s ease;
            }

            &:hover,
            &.active {
              background: $gray-2;
              color: $black;
              @include dark {
                background: $gray-8;
                color: $white;
              }
            }

            &.today {
              &:before {
                content: "";
                display: block;
                position: absolute;
                bottom: 2px;
                left: 50%;
                transform: translateX(-50%);
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background: $black;
                @include dark {
                  background: $white;
                }
              }
            }

            &.previous,
            &.next {
              color: $gray-3;
              @include dark {
                color: $gray-7;
              }
            }
          }
        }

        &.view-dates {
          .row {
            margin: 0;
          }
        }
      }
    }
  }
}
</style>
