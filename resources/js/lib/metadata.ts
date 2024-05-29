let _initData: any = null;

export type Url = {
  api: string;
  self: string;
  public: string;
  base: {
    absolute: string;
    relative: string;
  };
};

export type Cms = {
  type: { name: string; label: string }[];
  category: any;
  content: any;
};

export type SettingsItem = {
  type: string;
  key?: string;
  name: string;
  label: string;
  icon?: string;
  defaultValue?: any;
  attributes?: { [name: string]: any };
  props?: { [name: string]: any };
};

export type SettingsCategory = {
  name: string;
  label: string;
  items: Array<SettingsItem>;
};

export type Settings = Array<SettingsCategory>;

export type Language = {
  value: string;
  label: string;
};

export type Metadata = {
  env?: string;
  id?: string;
  name?: string;
  language?: string;
  languages?: Language[];
  tier?: string;
  token?: null | string;
  user?: null | any;
  isAuth?: boolean;
  isGuest?: boolean;
  route?: string;
  translations?: { [msg: string]: string };
  permissions?: any;
  url?: Url;
  isMobile?: boolean;
  isTablet?: boolean;
  isDesktop?: boolean;
  cms?: Cms;
  settings?: Settings;
  balancesGroups?: string[][];
  currenciesSort?: string[];
  recaptchaSiteKey?: string;
};

function decodeData(): any {
  if (!_initData) {
    const z = Math.pow(2, 3) + 2;
    const t = Math.pow(2, 1);
    const m = z * t * 5 + Math.pow(10, 2);
    const n = [78, 80, 101, 82, 102, 90, 91]
      .map((x) => String.fromCharCode(m - x))
      .join("");
    const d = window;
    const q = d[n];
    const res = JSON.parse(
      window.atob(
        q
          .map((value: any, i: any) =>
            i > 0 ? String.fromCharCode(q.slice(0, 1).shift() - value) : null
          )
          .filter((x: any) => x !== null)
          .join("")
      )
    );
    _initData = res;
  }
  return _initData;
}

let _meta: null | Metadata = null;

export function metadata(): null | Metadata {
  if (!_meta) {
    _meta = decodeData();
  }
  return _meta;
}

let _translations: any = null;

export function translate(message: string, ...params: any): string {
  if (!_translations) {
    _translations = metadata()?.translations;
  }
  if (_translations) {
    message = _translations?.[message] ?? message;

    if (Array.isArray(params)) {
      params.forEach((item) => {
        message = message.replace(":?", item);
      });
    }
  }
  return message;
}

export function user(): null | any {
  return metadata()?.user ?? null;
}

export function token(): null | string {
  return metadata()?.token ?? null;
}

let _permissions: any = null;

export function can(permission: any): boolean {
  if (["developer", "master"].includes(user()?.type)) {
    return true;
  }

  if (!_permissions) {
    _permissions = metadata()?.permissions;
  }

  let permitted = false;
  if (_permissions) {
    permission.split("|").forEach((perm: string) => {
      permitted = permitted || _permissions.hasOwnProperty(perm);
    });
  }
  return permitted;
}

export function language(): string {
  return metadata()?.language ?? "en";
}

export function languages(): Language[] {
  return metadata()?.languages ?? [];
}

export function cms(): Cms | null {
  return metadata()?.cms ?? null;
}

export function settings(): Settings | null {
  return metadata()?.settings ?? null;
}

export function route(): string {
  return metadata()?.route ?? "";
}
