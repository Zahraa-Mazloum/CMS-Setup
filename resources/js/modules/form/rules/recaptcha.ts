import { Rule } from "../lib/rule";

declare var window: any;

export class RecaptchaRule extends Rule {
  public widget: any = null;

  get defaultMessage(): string {
    return "{attribute} not verified";
  }
  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      const response = window.grecaptcha.getResponse(this.widget);
      resolve(response);
    });
  }
}
