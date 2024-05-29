import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { text, caption, alignment } = this.block.data

		return `
            <div class="text" style="text-align:${alignment}">${text}</div>
            <div class="caption">${caption}</div>
        `
	}
}
