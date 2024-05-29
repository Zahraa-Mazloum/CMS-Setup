import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { text, level, alignment } = this.block.data

		return `
            <h${level} style="text-align: ${alignment};">${text}</h${level}>
        `
	}
}
