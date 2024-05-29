import { Rule } from "../lib/rule";
import { Field } from "../lib/field";
import { createPrivateField } from "@/lib/private";

type Validation<T> = (rule: T, comparedField: Field) => boolean;

const [comparedField, setComparedField] = createPrivateField<
  CompareRule,
  Field
>();

const [validation, setValidation] = createPrivateField<
  CompareRule,
  Validation<CompareRule>
>();

export class CompareRule extends Rule {
  init() {
    super.init();

    setValidation(this, () => {
      return (rule: this, comparedField: Field) =>
        rule.field.value === comparedField.value;
    });
  }

  compareWith(field: Field): this {
    setComparedField(this, field);
    return this;
  }

  validation(callback: (rule: this, comparedField: Field) => boolean) {
    setValidation(this, () => callback);
  }

  get defaultMessage(): string {
    return `{attribute} is not matched ${comparedField(this).label}`;
  }
  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      resolve(!this.field.value || validation(this)(this, comparedField(this)));
    });
  }
}
