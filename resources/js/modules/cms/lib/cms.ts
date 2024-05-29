import { cms } from '@/lib/metadata';

export const splitType = (type): {id: null | number, name: string} => {
	let name: string = type
    let id:null | number = null
	let types = type.split(/\[(.*)\](.*)/).filter((element: any) => element)
	if (types.length > 1) {
		id = +types[0]
		name = types[1]
    }

    return { id, name }
}

export function maxContents (type) {
    const { name } = splitType(type)
    let hasMax = true
    Object.entries(cms()?.content).forEach(([key, config]: any) => {
        const names = key.split('.');
        if (names[0] === name) {
            hasMax = hasMax && (cms()?.content?.[key]?.max > 0)
        }
    })
    return hasMax
}
