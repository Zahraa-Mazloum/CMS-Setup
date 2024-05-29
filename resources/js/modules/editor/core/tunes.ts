import * as list from "@/modules/editor/tunes"

export function tunes(content: string, block: any) {
	if (
		Object.hasOwn(block.tunes, "textVariant") &&
		Object.hasOwn(list, "textVariant")
	) {
		const variant = list.textVariant.default?.[block.tunes?.textVariant] ?? null
		if (variant) {
			return variant(content)
		}
	}
	return content
}
