import { View } from "@/modules/editor/core/view"

export default class extends View {
	render(): string {
		const { items, style } = this.block.data

        const tg = style === 'ordered' ? 'ol' : 'ul'

		return renderList(tg, items)
	}
}

function renderList(tg, items){
    return `
            <${tg}>
                ${items.map(({ content, items = [] }: any) => `<li><div>${content}</div>${renderList(tg, items)}</li>`).join('')}
            </${tg}>
        `
}
