import { Field } from "@/modules/form/lib/field"
import { Model } from "@/modules/form/lib/model"
import { RequiredRule } from "@/modules/form/rules/required"

export class AuthorModel extends Model {

    get name (): Field {
        return this.field('name', 'Name', this.initData?.name ?? "", "The complete name of author")
    }

    get bio (): Field {
        return this.field('bio', 'Bio', this.initData?.bio ?? "")
    }

    get image (): Field {
        return this.field('image', 'Image', this.initData?.image ?? null)
    }

	get rules(): any[] {
        return [
            [[this.name], RequiredRule]
        ]
	}
}
