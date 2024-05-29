import { icon } from "@/lib/helper/strings"
import { View } from "@/modules/editor/core/view"
import {  mdiCheckboxBlankOutline, mdiCheckboxMarkedOutline } from "@mdi/js"

export default class extends View {
	render(): string {
		const { items, style } = this.block.data

        const tg = style === 'ordered' ? 'ol' : 'ul'

		return `
            <${tg}>
                ${items.map(({ text, checked }: any) => `<li class="${checked?'checked':''}">${icon({path: checked?mdiCheckboxMarkedOutline:mdiCheckboxBlankOutline})}&nbsp;${text}</li>`).join('')}
            </${tg}>
        `
	}
}
