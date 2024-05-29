/**
 * NavbarMDB.vue Startup File
 *
 * @project Startup Project
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Wednesday, May 29, 2024 at 12:17 PM GMT+3
 *
 */

import { createApp } from "@/lib/app"
import AppComponent from "../apps/NavbarMDB.vue"
import bootstrap from "../bootstrap/NavbarMDB"

const app = createApp("navbarmdb", AppComponent)
bootstrap(app)
app.mount()
