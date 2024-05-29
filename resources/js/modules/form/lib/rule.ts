import { Base } from "@/lib/base";
import { EventDispatcher } from "@/lib/event-dispatcher";
import { Field } from "./field";
import { createPrivateField } from "@/lib/private";
import { isPromise } from '@/lib/helper/functions';

type Events = {
  beforeValidate: EventDispatcher;
  afterValidate: EventDispatcher;
};

const [message, setMessage] = createPrivateField<Rule, string>()
const [skipOnEmpty, setSkipOnEmpty] = createPrivateField<Rule, boolean>(false)
const [enabled, setEnabled] = createPrivateField<Rule, (rule: Rule) => boolean>(
	(rule: Rule) => true,
)


export abstract class Rule extends Base<Events> {
  constructor(public field: Field) {
    super();
    this.init();
  }

  public init() {}

  public static assign<T extends Rule>(
		this: new (field: Field) => T,
		field: Field,
	) {
		const rule = new this(field)
		field.rule(rule)
		return rule
	}

	abstract get defaultMessage(): string
	abstract validate(): Promise<boolean>

	enabled(callback: (rule: Rule) => boolean = (rule: Rule) => true): this {
		setEnabled(this, callback)
		return this
	}

	get isEnable(): boolean {
		const check = enabled(this)
		if (typeof check === "function") {
			return check(this)
		}
		return check
	}

	skipOnEmpty(): this {
		setSkipOnEmpty(this, true)
		return this
	}

	get isSkipOnEmpty(): boolean {
		return skipOnEmpty(this)
	}

	public message(message: string | any[] | ((rule: this) => string)): this {
		let msg: any = null
		if (typeof message === "string") {
			msg = message
		} else if (Array.isArray(message)) {
			const [obj, method] = message
			msg = obj[method](this)
		} else if (typeof message === "function") {
			msg = message(this)
		}
		if (isPromise(msg)) {
			msg.then((m: string) => {
				setMessage(this, m)
			})
		} else {
			setMessage(this, msg)
		}
		return this
	}

	public createErrorMessage(name: string) {
		return (message(this) ?? this.defaultMessage).replace(
			new RegExp("{attribute}", "gm"),
			name,
		)
	}

	execute(): void {}

	runValidation(): Promise<{
		valid: boolean
		field: Field
		message: string | null
	}> {
		return new Promise(async (resolve) => {
			let valid: boolean = true
			let message: string | null = null
			if (this.isEnable) {
				this.execute()
				valid = await this.validate()
				if (!valid) {
					message = this.createErrorMessage(this.field.label)
				}
			}
			resolve({ valid, field: this.field, message })
		})
	}
}
