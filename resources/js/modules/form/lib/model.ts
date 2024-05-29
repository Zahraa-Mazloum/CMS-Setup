import { EventDispatcher } from "@/lib/event-dispatcher";
import { Field } from "./field";
import { Rule } from "./rule";
import { createPrivateField } from "@/lib/private";
import { Base } from "@/lib/base";
import { metadata } from "@/lib/metadata";

type ErrorType = { [name: string]: Field };
type RelationErrorType = { [name: string]: ErrorType };
type RelationsErrorType = {
  [name: string]: { index: number; errors: ErrorType }[];
};

type Events = {
  beforeValidate: EventDispatcher<{ sender: Model; field?: Field }>;
  afterValidate: EventDispatcher<{ sender: Model; field?: Field }>;
  beforeUpdate: EventDispatcher;
  afterUpdate: EventDispatcher;
};

export type DataList = {
  [name: string]: any;
};

const [fields, setFields] = createPrivateField<
  Model,
  { [name: string]: Field }
>({});

const [errors, setErrors] = createPrivateField<
  Model,
  { [name: string]: string[] }
>({});

const [models, setModels] = createPrivateField<
  Model,
  {
    [name: string]: {
      factory: (initData: DataList) => Model;
      model: Model | Model[];
      unlimited: boolean;
    };
  }
>({});

export abstract class Model extends Base<Events> implements Iterable<Field> {
  validationRules: Rule[] = [];

  public parent: Model;

  constructor(public initData: DataList, init: (self: any) => void = () => {}) {
    super();

    init(this);

    this.beforeCreate();

    // Assign fields from getters
    this.assignFieldsFromGetters();

    // generate Rules
    this.generateRules();

    this.afterCreate();
  }

  public beforeCreate() {}

  public afterCreate() {}

  public [Symbol.iterator]() {
    let i = -1;
    return {
      next: function () {
        i++;
        return {
          done: this.fields.length === i,
          value: this.fields.at(i),
        };
      }.bind(this),
    };
  }

  private assignFieldsFromGetters() {
    Object.getOwnPropertyNames(Object.getPrototypeOf(this))
      .filter((prop: any) => this[prop] instanceof Field)
      .forEach((prop: any) => {
        const field = this[prop];
        this.setField(field);
      });
    this.data = this.initData;
  }

  private generateRules() {
    for (let definition of this.rules) {
      let [fields, rule, callback = (rule: Rule) => rule] = definition;
      fields.forEach((field: Field) => {
        this.validationRules.push(callback(rule.assign(field)));
      });
    }
  }

  protected setField(field: Field) {
    setFields(this, (value) => {
      field.parent = this;
      value[field.name] = field;
      return value;
    });
  }

  createField(name: string, label: string, value: any = "") {
    Object.defineProperty(this, name, {
      writable: false,
      value: this.field(name, label, value),
    });
  }

  get fields(): any[] {
    return [
      ...Object.values(fields(this)),
      ...Object.values(models(this)).map((item) => item.model),
    ];
  }

  get length(): number {
    return this.fields.length;
  }

  abstract get rules(): any[];

  updateData(data: { [name: string]: any }) {
    for (let name in fields(this)) {
      if (data.hasOwnProperty(name)) {
        fields(this)[name].update(data[name]);
      }
    }

    setModels(this, (models) => {
      for (let name in models) {
        if (data.hasOwnProperty(name)) {
          if (Array.isArray(models[name].model)) {
            if (Array.isArray(data[name])) {
              data[name].forEach((item, i) => {
                if (!models[name].model[i]) {
                  if (models[name].unlimited) {
                    (models[name].model as Array<Model>).push(
                      models[name].factory(item)
                    );
                  }
                }
              });
            }
          } else {
            (models[name].model as Model).data = data[name];
          }
        }
      }
      return models;
    });
  }

  set data(data: { [name: string]: any }) {
    this.updateData(data);
  }

  generateData(): { [name: string]: any } {
    return {
      ...this.objectToData(
        fields(this),
        (models, k) => models[k],
        (field) => field.value
      ),
      ...this.objectToData(
        models(this),
        (models, k) => models[k].model,
        (model) => model.data
      ),
    };
  }

  get data(): { [name: string]: any } {
    return this.controlData(this.generateData());
  }

  controlData(list: { [name: string]: any }): { [name: string]: any } {
    return list;
  }

  protected objectToData(
    obj,
    model: (obj: any, k: any) => any = (obj, k) => obj[k],
    value: (obj: any) => any = (item: any) => item.data
  ) {
    return Object.keys(obj)
      .map((k) => {
        const modelItem = model(obj, k);
        if (Array.isArray(modelItem)) {
          return {
            [k]: modelItem.map((item) => value(item)),
          };
        }
        return { [k]: value(modelItem) };
      })
      .reduce((list, value) => {
        list = { ...list, ...value };
        return list;
      }, {});
  }

  get relations(): {
    [name: string]: {
      factory: (initData: DataList) => Model;
      model: Model | Model[];
      unlimited: boolean;
    };
  } {
    return models(this);
  }

  findField(name: string) {
    return fields(this)?.hasOwnProperty(name) ? fields(this)[name] : null;
  }

  protected field(
    name: string,
    label: string,
    value: any = null,
    hint: string = ""
  ): Field {
    return this.findField(name) ?? new Field(name, label, value, hint);
  }

  protected hasOne<T extends Model | Array<Model>>(
    name: string,
    factory: (initData: DataList) => Model
  ): T {
    setModels(this, (models) => {
      if (!models.hasOwnProperty(name)) {
        models[name] = {
          factory,
          model: factory(this.initData[name] ?? {}),
          unlimited: false,
        };
      }
      models[name].model["parent"] = this;
      return models;
    });
    return models(this)[name].model as T;
  }

  protected hasMany<T extends Array<Model>>(
    name: string,
    factory: (initData: DataList) => Model,
    initCount: number = 1
  ): T {
    setModels(this, (models) => {
      if (!models.hasOwnProperty(name)) {
        models[name] = {
          factory,
          model: new Array(initCount)
            .fill(null)
            .map((_, i: number) => factory(this.initData[name][i] ?? {}))
            .map((model: Model) => {
              model.parent = this;
              return model;
            }),
          unlimited: true,
        };
      }
      return models;
    });
    return models(this)[name].model as T;
  }

  protected hasLocals<T extends Array<Model>>(
    factory: (initData: DataList) => Model
  ): T {
    const name: string = "locals";
    setModels(this, (models) => {
      if (!models.hasOwnProperty(name)) {
        models[name] = {
          factory,
          model: metadata()
            ?.languages?.map?.((item: any) =>
              factory(
                this.initData[name]?.find(
                  (record: any) => record.language === item.value
                ) ?? { language: item.value }
              )
            )
            ?.map?.((model: Model) => {
              model.parent = this;
              return model;
            }),
          unlimited: false,
        };
      }
      return models;
    });
    return models(this)[name].model as T;
  }

  get errors(): ErrorType | RelationErrorType | RelationsErrorType {
    const errorsList: any = errors(this);
    for (let name in this.relations) {
      const { model } = this.relations[name];
      if (Array.isArray(model)) {
        for (let i in model) {
          const mdl = model[i];
          if (mdl.hasErrors) {
            if (!errorsList[name]) {
              errorsList[name] = [];
            }
            errorsList[name].push({
              index: i,
              errors: mdl.errors,
            });
          }
        }
      } else {
        if (model.hasErrors) {
          errorsList[name] = model.errors;
        }
      }
    }
    return errorsList;
  }

  get hasErrors(): boolean {
    return Object.keys(errors(this)).length > 0;
  }

  validate(): Promise<boolean> {
    return new Promise(async (resolve) => {
      this.events.beforeValidate.dispatch({ sender: this });
      this.fields.forEach((field: Field | Field[]) => {
        if (Array.isArray(field)) {
          field.forEach((f: Field) => {
            f.events?.beforeValidate?.dispatch({ sender: this, field: f });
          });
        } else {
          field.events?.beforeValidate?.dispatch({
            sender: this,
            field: field,
          });
        }
      });
      const valid = await this.validateModel(this);
      this.events.afterValidate.dispatch({ sender: this });
      this.fields.forEach((field: Field | Field[]) => {
        if (Array.isArray(field)) {
          field.forEach((f: Field) => {
            f.events?.afterValidate?.dispatch({ sender: this, field: f });
          });
        } else {
          field.events?.afterValidate?.dispatch({ sender: this, field: field });
        }
      });
      resolve(valid);
    });
  }

  validateModel(model): Promise<boolean> {
    return new Promise(async (resolve) => {
      let isValid: boolean = true;
      // validate fields
      model.validationRules.forEach((rule: Rule) => {
        rule.field.clearErrors();
      });
      for await (const rule of model.validationRules) {
        const { valid, field, message } = await rule.runValidation();
        isValid = isValid && valid;

        if (!valid) {
          setErrors(this, (errors) => {
            if (!errors.hasOwnProperty(field.name)) {
              errors[field.name] = [];
            }
            errors[field.name].push(message);
            return errors;
          });
          rule.field.addError(message);
          rule.field.forceUpdate();
        }
      }

      // validates relation models
      for (let name in model.relations) {
        const subModel = model.relations[name].model;
        if (Array.isArray(subModel)) {
          for await (let mdl of subModel) {
            const valid = await mdl.validate();
            isValid = isValid && valid;
          }
        } else {
          const valid = await subModel.validate();
          isValid = isValid && valid;
        }
      }

      resolve(isValid);
    });
  }

  get primarykey(): any {
    return this.findField("id")?.value ?? null;
  }

  get isNewRecord(): boolean {
    return !this.primarykey;
  }

  addRemoteErrors(catchedErr) {
    if (
      Object.hasOwn(catchedErr, "xhr") &&
      Object.hasOwn(catchedErr.xhr, "responseJSON") &&
      typeof catchedErr.xhr.responseJSON === "object" &&
      Object.hasOwn(catchedErr.xhr.responseJSON, "details") &&
      typeof catchedErr.xhr.responseJSON.details === "object"
    ) {
      const flds = fields(this);
      Object.keys(catchedErr.xhr.responseJSON.details).forEach(
        (name: string) => {
          if (Object.hasOwn(flds, name)) {
            const errors = catchedErr.xhr.responseJSON.details[name];
            flds[name].addError(...errors);
            flds[name].forceUpdate();
          }
        }
      );
    }
  }

  getField(name: string): Field {
    return this[name] ?? null;
  }
}
