import Toast from "../components/Toast.vue";
import type { Expose, Props, Slots } from "../components/Toast.vue";
import { createComponent } from "@/lib/create-component";


export const ToastController = createComponent<Props, Expose, Slots>(Toast)
