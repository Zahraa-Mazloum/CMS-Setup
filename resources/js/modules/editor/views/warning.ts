import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { title, message } = this.block.data

		return `
            <div class="alert alert-warning">
                <h3>${title}</h3>
                <p>${message}</p>
            </div>
        `
	}
}
