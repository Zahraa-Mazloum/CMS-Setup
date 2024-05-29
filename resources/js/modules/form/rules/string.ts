import { createPrivateField } from "@/lib/private";
import { Rule } from "../lib/rule";

const [trim, setTrim] = createPrivateField<StringRule, true>();
const [min, setMin] = createPrivateField<StringRule, number>();
const [max, setMax] = createPrivateField<StringRule, number>();
const [length, setLength] = createPrivateField<StringRule, number>();

export class StringRule extends Rule {
  #trim: boolean;
  #min: number;
  #max: number;
  #length: number;

  get isString(): boolean {
    return typeof this.field.value === "string";
  }

  execute(): void {
    if (this.isString && trim(this)) {
      this.field.silentUpdate(this.field.value.trim());
    }
  }

  trim() {
    setTrim(this, true);
    return this;
  }

  min(value: number) {
    setMin(this, value);
    return this;
  }

  max(value: number) {
    setMax(this, value);
    return this;
  }

  length(value: number) {
    setLength(this, value);
    return this;
  }

  get defaultMessage(): string {
    if (!this.isString) {
      return "{attribute} should be a string";
    } else if (
      typeof min(this) !== "undefined" &&
      typeof max(this) !== "undefined" &&
      (this.field.value.length < min(this) ||
        this.field.value.length > max(this))
    ) {
      return `{attribute} should be between ${min(this)} and ${max(
        this
      )} characters.`;
    } else if (
      typeof min(this) !== "undefined" &&
      this.field.value.length < min(this)
    ) {
      return `{attribute} should be bigger than or equal ${min(
        this
      )} characters.`;
    } else if (
      typeof max(this) !== "undefined" &&
      this.field.value.length > max(this)
    ) {
      return `{attribute} should be less than or equal $max(this)} characters.`;
    } else if (
      typeof length(this) !== "undefined" &&
      this.field.value.length !== length(this)
    ) {
      return `{attribute} should be equal ${length(this)} characters.`;
    }
    return "";
  }
  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      resolve(
        this.isString &&
          ((!this.field.value && this.isSkipOnEmpty) ||
            ((typeof min(this) === "undefined" ||
              this.field.value.length >= min(this)) &&
              (typeof max(this) === "undefined" ||
                this.field.value.length <= max(this)) &&
              (typeof length(this) === "undefined" ||
                this.field.value.length === length(this))))
      );
    });
  }
}
