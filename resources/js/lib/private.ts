type GetType<T, Q> = (sender: T) => Q;
type SetType<T, Q> = (sender: T, value: any | ((value: Q) => Q)) => void;
type SetValue<T> = T extends any ? T | (() => T) : boolean;

export function createPrivateField<T extends {}, Q extends unknown = unknown>(
  initialValue?: Q
): [GetType<T, Q>, SetType<T, Q>] {
  const fields = new WeakMap<T, Q>();

  const get = (sender: T): Q => {
    if (!fields.has(sender)) {
      let value: any =
        typeof initialValue === "function" ? initialValue() : initialValue;
      if (Array.isArray(value)) {
        value = [...value];
      } else if (typeof value === "object") {
        value = { ...value };
      }
      fields.set(sender, value);
    }
    return fields.get(sender) as Q;
  };

  const set = (sender: T, value: SetValue<Q>): void => {
    if (typeof value === "function") {
      fields.set(sender, value(get(sender)));
    } else {
      fields.set(sender, value as Q);
    }
  };

  const out: [GetType<T, Q>, SetType<T, Q>] = [get, set];

  return out;
}
