import { translate } from "@/lib/metadata"
import { Field } from "@/modules/form/lib/field"
import { Model } from "@/modules/form/lib/model"
import { CompareRule } from "@/modules/form/rules/compare"
import { RequiredRule } from "@/modules/form/rules/required"
import { StringRule } from "@/modules/form/rules/string"

export class User extends Model {
	get id(): Field {
		return this.field("id", translate("ID"), this.initData?.id ?? "")
	}

	get username(): Field {
		return this.field(
			"username",
			translate("Username"),
			this.initData?.username ?? "",
		)
	}

	get password(): Field {
		return this.field("password", translate("Password"), "")
	}

	get confirmPassword(): Field {
		return this.field("confirm_password", translate("Confirm Password"), "")
	}

	get status(): Field {
		return this.field(
			"status",
			translate("Status"),
			this.initData?.status ?? "enabled",
		)
	}

	get rules(): any[] {
		return [
			[
				[this.username, this.password, this.confirmPassword],
				StringRule,
			],
			[[this.username], RequiredRule],
			[
				[this.password, this.confirmPassword],
				RequiredRule,
				(required: RequiredRule) => required.enabled(() => this.isNewRecord),
			],
			[
				[this.confirmPassword],
				CompareRule,
				(compare: CompareRule) => compare.compareWith(this.password),
			],
		]
	}
}
