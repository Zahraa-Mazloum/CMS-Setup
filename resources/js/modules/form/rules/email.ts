import { Rule } from "../lib/rule";

export class EmailRule extends Rule {
  get defaultMessage(): string {
    return "{attribute} is invalid email";
  }
  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      resolve(
        !this.field.value ||
          this.regExp.test(String(this.field.value).toLowerCase())
      );
    });
  }

  get regExp(): RegExp {
    return /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  }
}
