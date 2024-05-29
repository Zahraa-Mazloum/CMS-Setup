import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const {
			service,
			source,
			embed,
			width = 700,
			height = 400,
			caption,
		} = this.block.data

		return `
            <div style="display: flex; width: 100%; height: ${height}px; flex-direction: column; overflow: hidden;">
                <iframe src="${embed}" width="${width}" height="${height}" style="--width:${width}px;--height:${height}px; flex-grow: 1; border: none; margin: 0; padding: 0;" data-url="${source}"></iframe>
                ${caption ? `<div class="caption">${caption}</div>` : ''}
            </div>
        `
	}

	get classes(): string[] {
		const { service } = this.block.data
		return [service]
	}
}
