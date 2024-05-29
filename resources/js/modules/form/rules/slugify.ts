import { slugify } from "@/lib/helper/strings";
import { Rule } from "../lib/rule";

export class SlugifyRule extends Rule {
  get defaultMessage(): string {
    return "{attribute} is not slugify";
  }

  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      resolve(true);
    });
  }

  execute(): void {
    const suffix = this.field.value.split("").at(-1) === " " ? " " : "";
    this.field.update(slugify(this.field.value) + suffix);
  }
}
