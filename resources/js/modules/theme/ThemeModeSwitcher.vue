<script setup lang="ts">
import { mdiThemeLightDark, mdiWeatherNight, mdiWeatherSunny } from "@mdi/js";
import Icon from "@/components/Icon.vue";
import { useThemeStore } from "./index";
import { storeToRefs } from "pinia";

const themeStore = useThemeStore();

const { theme } = storeToRefs<any>(themeStore);
</script>
<template>
  <div
    class="theme-switcher"
    :class="{ [theme]: true }"
    @click.prevent.stop="themeStore.changeTheme"
  >
    <div class="theme">
      <template v-if="theme === 'light'">
        <Icon :path="mdiWeatherSunny" />
      </template>
      <template v-if="theme === 'dark'">
        <Icon :path="mdiWeatherNight" />
      </template>
      <template v-if="theme === 'auto'">
        <Icon :path="mdiThemeLightDark" />
      </template>
    </div>
  </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";
.theme-switcher {
  position: relative;
  cursor: pointer;
  border-radius: 10px;
  display: flex;
  flex-direction: row;
  align-items: center;
  width: 55px;
  height: 30px;
  padding: 0 5px;
  transition: all 0.25s ease;
  background: $gray-3;
  @include dark {
    background: $gray-8;
  }

  .theme {
    width: 30px;
    height: 30px;
    color: $black;
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    @include dark {
      color: $white;
    }
  }

  &.light {
    justify-content: flex-start;
  }
  &.dark {
    justify-content: flex-end;
  }
  &.auto {
    justify-content: center;
  }
}
</style>
