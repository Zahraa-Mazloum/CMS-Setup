import { Rule } from "../lib/rule";

export class RequiredRule extends Rule {
  get defaultMessage(): string {
    return "{attribute} is required";
  }
  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      resolve(
        this.field.value !== "" &&
          this.field.value !== null &&
          this.field.value !== undefined
      );
    });
  }
}
