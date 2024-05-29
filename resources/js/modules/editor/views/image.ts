import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { file, caption } =
			this.block.data

		return `
            <figure class="figure" data-size="${file.size}" data-width="${
			file.width
		}" data-height="${file.height}" data-mime="${file.mime}" data-bit="${
			file.bit
		}">
                <img src="${file.url}" alt="${
			caption || file.url
		}" style="max-width: 100%;" >
                ${(caption && `<figcaption>${caption}</figcaption>`) || ""}
            </figure>
        `
	}

	get classes(): string[] {
		const { withBorder, stretched, withBackground } = this.block.data

		const out: string[] = []
		if (withBorder === "true") {
			out.push("image-border")
		}
		if (stretched === "true") {
			out.push("image-stretched")
		}
		if (withBackground === "true") {
			out.push("image-bg")
		}
		return out
	}
}
