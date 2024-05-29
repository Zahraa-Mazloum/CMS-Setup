/**
 * Admins.vue Startup File
 *
 * @project Startup Project
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Tuesday, May 28, 2024 at 11:04 AM GMT+3
 *
 */

import { createApp } from "@/lib/app"
import AppComponent from "../apps/Admins.vue"
import bootstrap from "../bootstrap/Admins"

const app = createApp("admins", AppComponent)
bootstrap(app)
app.mount()
