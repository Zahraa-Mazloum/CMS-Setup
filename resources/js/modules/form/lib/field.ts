import type { ComponentInternalInstance } from "vue";
import { Model } from "./model";
import { Rule } from "./rule";
import { createPrivateField } from "@/lib/private";
import { EventDispatcher } from "@/lib/event-dispatcher";
import { Base } from "@/lib/base";
import { RequiredRule } from "../rules/required";

const [name, setName] = createPrivateField<Field, string>("");
const [label, setLabel] = createPrivateField<Field, string>("");
const [value, setValue] = createPrivateField<Field, any>(null);
const [hint, setHint] = createPrivateField<Field, string>("");
const [extra, setExtra] = createPrivateField<Field, {[name: string]: any}>({});
const [errors, setErrors] = createPrivateField<Field, string[]>([]);
const [rules, setRules] = createPrivateField<Field, Rule[]>([]);
const [touch, setTouch] = createPrivateField<Field, boolean>(false);
const [blur, setBlur] = createPrivateField<Field, boolean>(false);
const [component, setComponent] = createPrivateField<
  Field,
  ComponentInternalInstance | null
>(null);

type Events = {
  beforeValidate: EventDispatcher;
  afterValidate: EventDispatcher;
  beforeUpdate: EventDispatcher;
  afterUpdate: EventDispatcher;
  beforeError: EventDispatcher;
  afterError: EventDispatcher;
};

const runBeforeUpdate = (
  context: Field | Model,
  originalContext: any = null
) => {
  if (!originalContext) {
    originalContext = context;
  }
  context.events.beforeUpdate.dispatch({ sender: originalContext });
  if (context.hasOwnProperty("parent") && context["parent"]) {
    runBeforeUpdate(context["parent"], originalContext);
  }
};

const runAfterUpdate = (
  context: Field | Model,
  originalContext: any = null
) => {
  if (!originalContext) {
    originalContext = context;
  }
  context.events.afterUpdate.dispatch({ sender: originalContext });
  if (context.hasOwnProperty("parent") && context["parent"]) {
    runAfterUpdate(context["parent"], originalContext);
  }
};

export class Field extends Base<Events> {
  public parent: Model;

  constructor(name: string, label: string, value: any, hint: string = "") {
    super();
    setName(this, name);
    setLabel(this, label);
    setValue(this, value);
    setHint(this, hint);
    setExtra(this, {});
  }

  update(value: any | ((value: any) => void)): void {
    runBeforeUpdate(this);
    this.silentUpdate(value);
    if (touch(this)) {
      this.validate();
    }
    runAfterUpdate(this);
  }

  silentUpdate(value: any | ((value: any) => void)): void {
    setValue(this, value);
  }

  public get name(): string {
    return name(this);
  }

  public get label(): string {
    return label(this);
  }

  public get value(): any {
    return value(this);
  }

  public get hint(): string {
    return hint(this);
  }

  public setExtra(callback: (value: any) => {[name: string]: any}){
    setExtra(this, callback(extra(this)));
  }

  public get extra(): any{
    return extra(this);
  }

  get htmlLabel(): string {
    return `
        <span>${this.label}</span>
        ${
          this.rules.filter(
            (rule) => rule instanceof RequiredRule && rule.isEnable
          ).length > 0
            ? `<strong class="required text-danger" title="${this.label} is required">*</strong>`
            : ""
        }
    `;
  }

  rule(rule: Rule) {
    setRules(this, (rules: Rule[]) => {
      rules.push(rule);
      return rules;
    });
    return this;
  }

  get rules(): Rule[] {
    return rules(this);
  }

  addError(...messages: string[]) {
    setErrors(this, (previousMessages: string[]) => [
      ...new Set(previousMessages.concat(messages)),
    ]);
  }

  get errors(): string[] {
    return errors(this);
  }

  clearErrors() {
    setErrors(this, []);
  }

  get hasErrors(): boolean {
    return this.errors.length > 0;
  }

  touch(e: any = null) {
    setTouch(this, true);
  }

  blur(e: any) {
    if (e.target.value) {
      setBlur(this, true);
    }
    if (blur(this)) {
      this.validate();
    }
  }

  set component(component: ComponentInternalInstance | null) {
    setComponent(this, component);
  }

  forceUpdate() {
    component(this)?.proxy?.$forceUpdate();
  }

  toString(): string {
    return JSON.stringify(
      {
        name: this.name,
        label: this.label,
        value: this.value,
        hint: this.hint,
      },
      null,
      2
    );
  }

  validate(): Promise<boolean> {
    return new Promise((resolve) => {
      this.events.beforeValidate.dispatch({ sender: this });
      this.clearErrors();
      Promise.all<{
        valid: boolean;
        field: Field;
        message: string | null;
      }>(this.rules.map((rule) => rule.runValidation())).then((values) => {
        const valid = values.reduce((value, currentValue) => {
          return value && currentValue.valid;
        }, true);
        if (!valid) {
          const messages: any = values
            .filter((value) => value.message !== null)
            .map((value) => value.message);
          this.events.beforeError.dispatch({ sender: this, messages });
          this.addError(...messages);
          this.events.afterError.dispatch({ sender: this, messages });
        }
        this.events.afterValidate.dispatch({ sender: this, valid });
        this.forceUpdate();
        resolve(valid);
      });
    });
  }
}
