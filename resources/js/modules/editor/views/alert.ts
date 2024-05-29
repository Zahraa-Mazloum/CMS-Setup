import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { type, message } = this.block.data

		return `
            <div class="alert alert-${type}">${message}</div>
        `
	}
}
