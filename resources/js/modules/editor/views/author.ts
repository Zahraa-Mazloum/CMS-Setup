import { nl2br } from '@/lib/helper/strings';
import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { name, bio, image } = this.block.data

		return `
            <div class="card">
                <img src="${image.url}" class="card-img-top" >
                <div class="card-body">
                    <h5 class="card-title">${nl2br(name)}</h5>
                    <p class="card-text">${nl2br(bio)}</p>
                </div>
            </div>
        `
	}
}
