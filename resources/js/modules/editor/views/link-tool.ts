import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { link, meta : { image : { url }, title, description, keywords, site_name } } = this.block.data

		return `
            <a href="${link}" target="_blank" class="link-view">
                <div class="image" style="--image: url(${url});"></div>
                <div class="info">
                    <h4 class="title">${title}</h4>
                    <p class="description">${description}</p>
                    <div class="keywords">${keywords}</div>
                    <div class="site">${site_name}</div>
                </div>
            </a>
        `
	}
}
