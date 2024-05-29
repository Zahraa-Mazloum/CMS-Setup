/**
 * ThemeSwitcher.vue Bootstrap File
 *
 * @project yii2
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Monday, June 26, 2023 at 12:20 PM GMT+3
 *
 */
import { App } from "@/lib/app";
import { pinia } from "@/lib/pinia";

export default function (app: App) {
  app.events.mount.listen(({ vue }) => {
    vue?.use(pinia);
  });
}
