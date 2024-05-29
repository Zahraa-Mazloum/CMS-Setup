import Modal from "../components/Modal.vue";
import type { Expose, Props, Slots } from "../components/Modal.vue";
import { createComponent } from "@/lib/create-component";


export const ModalController = createComponent<Props, Expose, Slots>(Modal)
