import { ContentLocal } from "./content-local"
import { Attrs } from "./attrs"
import { Model } from "@/modules/form/lib/model"
import { Field } from "@/modules/form/lib/field"
import { DataList } from "@/modules/form/types"

export class Content extends Model {
	get id(): Field {
		return this.field("id", "ID", this.initData?.id ?? null)
	}

	get type(): Field {
		return this.field("type", "Type", this.initData?.type ?? "default")
	}

	get status(): Field {
		return this.field("status", "Status", this.initData?.status ?? "active")
	}

	get position(): Field {
		return this.field("position", "Position", this.initData?.position ?? "0")
	}

	get categories(): Field {
		return this.field(
			"categories",
			"Categories",
			this.initData?.categories ?? [],
		)
	}

	get attrs1(): Attrs {
		return this.hasOne("attrs1", (initData: DataList) => {
			return Attrs.generate(
				this.initData?.type ?? "default",
				"1",
				this.initData.attrs,
			)
		})
	}

	get attrs2(): Attrs {
		return this.hasOne("attrs2", (initData: DataList) => {
			return Attrs.generate(
				this.initData?.type ?? "default",
				"2",
				this.initData.attrs,
			)
		})
	}

	get attrs3(): Attrs {
		return this.hasOne("attrs3", (initData: DataList) => {
			return Attrs.generate(
				this.initData?.type ?? "default",
				"3",
				this.initData.attrs,
			)
		})
	}

	get locals(): ContentLocal[] {
		return this.hasLocals(
			(initData: DataList) =>
				new ContentLocal(initData, (self: any) => {
					self.type = this.initData.type ?? "default"
				}),
		)
	}

	get rules(): any[] {
		return []
	}

	controlData(list: { [name: string]: any }): { [name: string]: any } {
		list.attrs = { ...list.attrs1, ...list.attrs2, ...list.attrs3 }
		delete list.attrs1
		delete list.attrs2
		delete list.attrs3
		return list
	}
}
