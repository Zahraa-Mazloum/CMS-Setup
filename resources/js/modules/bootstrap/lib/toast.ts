import { ToastController } from "@/modules/bootstrap/controllers/toast-controller";
import { h } from "vue";
import { ToastColor } from "../types";

const createToast = async (
  title: string,
  message?: string,
  color: null | ToastColor = null
) => {
  const toast = await ToastController(
    {
      position: "bottom-end",
      color,
      title,
      autohide: true,
      delay: 5000,
    },
    {
      ...(message
        ? {
            default: () => h("p", { innerHTML: message, class: "m-0" }, []),
          }
        : {}),
    }
  );
  toast.open();
};

export const toast = async (title: string, message?: string) => {
  return await createToast(title, message);
};

export const primaryToast = async (title: string, message?: string) => {
  return await createToast(title, message, "primary");
};

export const secondaryToast = async (title: string, message?: string) => {
  return await createToast(title, message, "secondary");
};

export const infoToast = async (title: string, message?: string) => {
  return await createToast(title, message, "info");
};

export const successToast = async (title: string, message?: string) => {
  return await createToast(title, message, "success");
};

export const warningToast = async (title: string, message?: string) => {
  return await createToast(title, message, "warning");
};

export const dangerToast = async (title: string, message?: string) => {
  return await createToast(title, message, "danger");
};

export const lightToast = async (title: string, message?: string) => {
  return await createToast(title, message, "light");
};

export const darkToast = async (title: string, message?: string) => {
  return await createToast(title, message, "dark");
};
