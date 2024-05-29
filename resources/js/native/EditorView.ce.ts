import { defineCustomElement } from 'vue';
import EditorView from "./EditorView.ce.vue";

customElements.define("web-view", defineCustomElement(EditorView));
