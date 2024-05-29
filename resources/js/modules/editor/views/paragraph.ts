import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { text, alignment } = this.block.data

		return `
            <p style="text-align: ${alignment};">${text}</p>
        `
	}
}
