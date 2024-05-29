/**
 * admin.ts Common Script File
 *
 * @project yii2
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Monday, July 10, 2023 at 11:49 AM GMT+3
 *
 */

import "@scss/tiers/admin/admin.scss";
import "@/native";

(() => {
  function main() {
    // TODO...
  }

  document.addEventListener("readystatechange", async (event: any) => {
    if (event.target.readyState === "complete") {
      (await import("@/modules/theme"))?.initTheme?.();
      main();
    }
  });
})();
