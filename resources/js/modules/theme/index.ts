import { setCookie } from "@/lib/helper/cookies";
import { metadata } from "@/lib/metadata";
import { defineStore } from "pinia";
import { computed, nextTick, ref } from "vue";

export const THEME_LIGHT = 0;
export const THEME_DARK = 1;
export const THEME_AUTO = 2;

export const themes = ["light", "dark", "auto"];

export const getStoredTheme = () =>
  localStorage.getItem(`theme-${metadata?.()?.tier}`);
export const setStoredTheme = (theme) => {
  localStorage.setItem(`theme-${metadata?.()?.tier}`, theme);
};

export const getPreferredTheme = () => {
  const storedTheme = getStoredTheme();
  if (storedTheme) {
    return storedTheme;
  }

  return window.matchMedia("(prefers-color-scheme: dark)").matches
    ? "dark"
    : "light";
};

export const setTheme = (theme) => {
  if (
    theme === "auto" &&
    window.matchMedia("(prefers-color-scheme: dark)").matches
  ) {
    document.documentElement.setAttribute("data-bs-theme", "dark");
    setCookie(`theme-${metadata?.()?.tier}`, "dark", 73200);
  } else {
    document.documentElement.setAttribute("data-bs-theme", theme);
    setCookie(`theme-${metadata?.()?.tier}`, theme, 73200);
  }
};

export const initTheme = () => {
  setTheme(getPreferredTheme());
};

export const useThemeStore = defineStore("theme", () => {
  const themeInc = ref<number>(
    getPreferredTheme() === "dark"
      ? THEME_DARK
      : getPreferredTheme() === "light"
      ? THEME_LIGHT
      : THEME_AUTO
  );

  const theme = computed(() => {
    return themes[themeInc.value % 3];
  });

  const isDarkTheme = computed(() => {
    return (
      theme.value === "dark" ||
      (window.matchMedia("(prefers-color-scheme: dark)").matches &&
        theme.value === "auto")
    );
  });

  const changeTheme = () => {
    themeInc.value++;
    nextTick(() => {
      setStoredTheme(theme.value);
      setTheme(theme.value);
    });
  };
  return { theme, isDarkTheme, changeTheme };
});

export function isDark() {
  const themeStore = useThemeStore();
  return themeStore.isDarkTheme;
}

export function theme() {
  const themeStore = useThemeStore();
  return themeStore.theme;
}

window
  .matchMedia("(prefers-color-scheme: dark)")
  .addEventListener("change", () => {
    const storedTheme = getStoredTheme();
    if (storedTheme !== "light" && storedTheme !== "dark") {
      setTheme(getPreferredTheme());
    }
  });
