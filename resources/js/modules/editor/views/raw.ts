import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { html } = this.block.data

		return `
            ${html}
        `
	}
}
