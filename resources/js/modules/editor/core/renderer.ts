import * as list from "@/modules/editor/views"
import { tunes } from "./tunes"

export type Content = {
	time: string
	version: string
	blocks: any[]
}

export function render(content: Content) {
	const out: string[] = []
	content?.blocks?.forEach?.((item: any) => {
		if (Object.hasOwn(list, item.type)) {
			const cls = list[item.type].default
			const block = new cls(item)
			out.push(`
                <div id="${block.id}" class="block block-${item.type} ${block.classes.join(' ')}">
                    ${tunes(block.render(), item)}
                </div>
            `)
		}
	})
	return out.join("") ?? ""
}
