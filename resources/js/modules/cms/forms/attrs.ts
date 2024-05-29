import { createPrivateField } from '@/lib/private';
import { Model, DataList } from '@/modules/form/lib/model';
import { cms } from '@/lib/metadata';

const [fields, setFields] = createPrivateField<Attrs, any>({})

export class Attrs extends Model {
	static generate(
		type: string,
		column: string | number,
		initData: DataList = {},
        section: string = "content"
    ) {
        let typeName = type
        let types = type.split(/\[(.*)\](.*)/).filter((element: any) => element)
        if (types.length > 1) {
            typeName = types[1]
        }
		const fieldsList =
			cms()?.[section][typeName]?.attrs?.find(
				(row) => `${row.column}` === `${column}`,
			)?.fields ?? []
		const model = new Attrs(initData, (self: Attrs) => {
			setFields(self, fieldsList)
		})
		return model
	}

	get attrs() {
		return fields(this)
	}

	public beforeCreate() {
		const fieldsList = fields(this)
		fieldsList.forEach((data: any) => {
			this.createField(
				data.name,
				data.label,
				this.initData?.[data.name] ?? data.defaultValue ?? "",
			)
			const field = this[data.name]
			this.setField(field)
		})
	}

	get rules(): any[] {
		return []
	}
}
