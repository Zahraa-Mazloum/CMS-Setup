export function chunk(array: Array<any>, size: number) {
  const chunked_arr: any = [];
  let index = 0;
  while (index < array.length) {
    chunked_arr.push(array.slice(index, size + index));
    index += size;
  }
  return chunked_arr;
}

export function groupBy(
  array: Array<any>,
  name: string | ((item: any) => string)
) {
  return array.reduce((group, item) => {
    const value = typeof name === "function" ? name(item) : item[name];
    group[value] = group[value] ?? [];
    group[value].push(item);
    return group;
  }, {});
}

export function extract(
  array: Array<any>,
  name: string | ((item: any) => string)
) {
  return Object.values(
    array.reduce((list, item) => {
      const value = typeof name === "function" ? name(item) : item[name];
      if (!Object.hasOwn(list, value)) {
        list[value] = item;
      }
      return list;
    }, {})
  );
}

export type TreeDisplayOptions = {
  filterBy?: string;
  filterValue?: any;
  prefix?: string;
  level?: number;
  delimiter?: string;
};

export function flat2tree(
  items: Array<any>,
  filterBy: string = "parent_id",
  filterValue: any = null
) {
  const list: any = {};
  items.forEach((item: any) => {
    if (Object.hasOwn(item, filterBy)) {
      if (item[filterBy] === filterValue) {
        list[item.id] = {
          ...item,
          children: flat2tree(items, filterBy, item.id),
        };
        if (!list[item.id].children.length) {
          list[item.id].children = null;
        }
      }
    } else {
      throw new Error(`Property ${filterBy} not exist in object provided`);
    }
  });
  return Object.values(list);
}

export function displayTree(
  callback: (
    item: any,
    prefix: string,
    level: number,
    hasChildren: boolean
  ) => any,
  items: any[],
  {
    filterBy = "parent_id",
    filterValue = null,
    prefix = "",
    level = 0,
    delimiter = "|__",
  }: any
): any[] {
  let out: any = [];
  const chunk = items.filter((item: any) => item[filterBy] === filterValue);
  chunk.forEach((item: any) => {
    const children = displayTree(callback, items, {
      filterBy,
      filterValue: item.id,
      prefix: `${prefix}${delimiter}`,
      level: level + 1,
      delimiter,
    });
    out.push(callback(item, prefix, level, children.length > 0));
    out = [...out, ...children];
  });
  return out;
}
