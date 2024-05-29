import { autoId } from "@/lib/helper/strings"

export abstract class View {
	#id: string

	constructor(protected block: any) {
		this.#id = autoId("block")
	}

	get id(): string {
		return `${this.#id}-${this.block.id}`
	}
	abstract render(): string

	get classes(): string[] {
		return []
	}
}
