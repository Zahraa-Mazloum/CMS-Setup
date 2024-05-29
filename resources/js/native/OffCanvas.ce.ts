import { defineCustomElement } from 'vue';
import OffCanvas from "./OffCanvas.ce.vue";

customElements.define("web-offcanvas", defineCustomElement(OffCanvas));
