/**
 * Settings.vue Startup File
 *
 * @project Startup Project
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Tuesday, May 28, 2024 at 11:04 AM GMT+3
 *
 */

import { createApp } from "@/lib/app"
import AppComponent from "../apps/Settings.vue"
import bootstrap from "../bootstrap/Settings"

const app = createApp("settings", AppComponent)
bootstrap(app)
app.mount()
