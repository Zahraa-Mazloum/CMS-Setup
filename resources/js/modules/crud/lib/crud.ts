import { api } from "@/lib/api";
import { serialize } from "@/lib/helper/object";

export enum FieldType {
  Number = "number",
  String = "string",
  Enum = "enum",
}

export type Field<S extends string = string> = {
  type: FieldType;
  name: S;
  label: string;
  sortable?: boolean;
  searchable?: boolean;
  visible?: boolean;
  fitable?: boolean;
};

export type Fields<S extends string = string> = Array<Field<S>>;

export type FetchCallbackArgs = {
  page: number;
  perPage: number;
  keyword: any;
  sort: string;
  fields: Fields;
};

export type CrudResponse<T> = {
  data: T[];
  total?: number;
  page?: number;
  perPage?: number;
  pages?: number;
};

export type FetchCallback<T> = (
  args: FetchCallbackArgs
) => Promise<CrudResponse<T>>;

export const createResponse = <T>({ data, header }: any): CrudResponse<T> => {
  return {
    data,
    total: parseInt(header("x-pagination-total-count")) ?? 0,
    page: parseInt(header("x-pagination-current-page")) ?? 1,
    perPage: parseInt(header("x-pagination-per-page")) ?? 1,
    pages: parseInt(header("x-pagination-page-count")) ?? 0,
  };
};

export const buildFilter = (keyword, fields: Fields) => {
  if (keyword) {
    let filter: any = [];
    for (const field of fields) {
      if (field.searchable) {
        const obj: any = {};
        if (
          field.type === FieldType.Number &&
          /^\d+\.{0,1}\d{0,}$/.test(keyword as string)
        ) {
          obj[field.name] = keyword;
        } else if (
          field.type === FieldType.String ||
          field.type === FieldType.Enum
        ) {
          obj[field.name] = { like: keyword };
        }
        if (Object.keys(obj).length > 0) {
          filter.push(obj);
        }
      }
    }
    filter = { or: filter };
    return serialize(filter, "filter");
  }
  return "";
};

export type FetchResult<Item> = ({
  page,
  perPage,
  sort,
  keyword,
  fields,
}: any) => Promise<CrudResponse<Item>>;

export const fetch = <Item = {}>(
  path: string,
  expand?: string,
  params?: { [name: string]: any }
): FetchResult<Item> => {
  return ({
    page,
    perPage,
    sort,
    keyword,
    fields,
  }: any): Promise<CrudResponse<Item>> => {
    return new Promise((resolve, reject) => {
      const extraParams = () => {
        if (params && Object.keys(params).length > 0) {
          let p: any = [];
          for (let i in params) {
            if (typeof params[i] === "object") {
              p.push(serialize(params[i], i));
            } else {
              p.push(`${i}=${params[i]}`);
            }
          }
          return `&${p.join("&")}`;
        }
        return "";
      };
      const filters = buildFilter(keyword, fields);
      api()
        .get(
          `${path}?${
            expand ? `expand=${expand}&` : ""
          }page=${page}&per-page=${perPage}&sort=${sort}${
            filters ? `&${filters}` : ""
          }${extraParams()}`
        )
        .then((response: any) => {
          resolve(createResponse<Item>(response));
        })
        .catch((e) => {
          reject(e);
        });
    });
  };
};

export const createFields = <S extends string = string>(
  ...fields: Fields<S>
): Fields<S> => {
  return [...fields];
};
