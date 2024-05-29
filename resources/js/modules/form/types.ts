import { Field } from './lib/field';
import { UnwrapNestedRefs } from 'vue';

export type DataList = {
  [property in string]: unknown;
};

export type DefinitionField = {
  name: string;
  label: string;
  value: (data?: DataList) => unknown;
  hint?: string;
};

export type DefinitionFields<P extends string = string> = {
  [property in P]: DefinitionField;
};

export type Model<P extends string = string> = {
  [Property in P]: Field;
};

export type ModelUnwraper<T extends string = string> = UnwrapNestedRefs<Model<T>>;
