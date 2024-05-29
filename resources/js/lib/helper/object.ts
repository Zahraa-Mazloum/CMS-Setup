export function serialize(obj: any, prefix: any = null): string {
    const str: any = [];
    let p: any;
    for (p in obj) {
      if (obj.hasOwnProperty(p)) {
        const k = prefix ? `${prefix}[${p}]` : p;
        const v = obj[p];
        str.push(v !== null && typeof v === "object" ? serialize(v, k) : `${encodeURIComponent(k)}=${encodeURIComponent(v)}`);
      }
    }
    return str.join("&");
  }
  