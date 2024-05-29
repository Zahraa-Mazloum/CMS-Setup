import { createPrivateField } from "@/lib/private";
import { Model, DataList } from "../lib/model";

const [fields, setFields] = createPrivateField<Attrs, any>({});

export class Attrs extends Model {
  static generate(fieldsList: any[], initData: DataList = {}) {
    const model = new Attrs(initData, (self: Attrs) => {
      setFields(self, fieldsList);
    });
    return model;
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

  get attrs() {
		return fields(this)
	}

  get rules(): any[] {
    return [];
  }
}
