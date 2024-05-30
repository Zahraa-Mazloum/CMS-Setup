/**
 * Carousel.vue Startup File
 *
 * @project Startup Project
 * @company Codendot <https://codendot.com>
 * @author Jawdat Sobh <jawdat@codendot.com>
 * @since Wednesday, May 29, 2024 at 04:37 PM GMT+3
 *
 */

import { createApp } from "@/lib/app"
import AppComponent from "../apps/Carousel.vue"
import bootstrap from "../bootstrap/Carousel"

const app = createApp("carousel", AppComponent)
bootstrap(app)
app.mount()
