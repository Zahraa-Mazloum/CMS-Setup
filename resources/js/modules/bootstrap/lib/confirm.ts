import { ModalController } from "../controllers/modal-controller";
import { h } from "vue";
import { autoId } from "@/lib/helper/strings";
import Icon from "@/components/Icon.vue";
import { mdiCheckBold } from "@mdi/js";
import Button from '@/modules/bootstrap/components/Button.vue';

const createConfirm = (
  title: string,
  message?: string,
  noText: string = "No",
  yesText: string = "Yes"
): Promise<boolean> => {
  return new Promise(async (resolve) => {
    const id = autoId("confirm");
    const modal = await ModalController(
      {
        title,
        closeText: noText,
        onOpened: () => {
          document.querySelector(`#${id}`)?.addEventListener("click", () => {
            modal.close();
            resolve(true);
          });
        },
        onDismiss: () => {
          resolve(false);
        },
      },
      {
        ...(message ? { default: () => h("p", {}, message) } : {}),
        buttons: () => [
          h(Button, { class: ["btn", "btn-primary"], id }, [
            h(Icon, {path: mdiCheckBold, color: 'success'}, ''),
            yesText
          ]),
        ],
      }
    );
    modal.open();
  });
};

export function confirm(title: string, message?: string): Promise<boolean> {
  return createConfirm(title, message);
}
