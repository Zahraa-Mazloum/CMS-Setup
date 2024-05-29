import { dangerToast } from "@/modules/bootstrap/lib/toast";
import $ from "jquery";
import { metadata } from "./metadata";

const ajax = $.ajax;

declare var window;

export enum RequestType {
  GET = "get",
  POST = "post",
  PUT = "put",
  DELETE = "delete",
}

export enum RequestDataType {
  XML = "xml",
  JSON = "json",
  JSONP = "jsonp",
  SCRIPT = "script",
  HTML = "html",
  TEXT = "text",
}

export enum RequestContentType {
  XML = "text/xml",
  JSON = "application/json",
  JSONP = "application/jsonp",
  HTML = "text/html",
  TEXT = "text/plain",
}

export interface RequestOptions {
  params?: { [key: string]: any };
  data?: { [key: string]: any };
  type: RequestType;
  dataType?: RequestDataType;
  contentType?: RequestContentType;
  silent?: boolean;
  skipAuthorization?: boolean;
  track?: boolean;
  noLogout?: boolean;
  request?: (xhr: any) => {};
}

export function resolveUrl(path: string) {
  const baseUrl = metadata()?.url?.api;
  return `${baseUrl}${path}`;
}

function request(path: string, options: RequestOptions) {
  return new Promise((resolve, reject) => {
    const settings: any = {};
    settings.type = options.type;
    settings.cache = false;
    if ([RequestType.GET, RequestType.DELETE].includes(options.type)) {
      settings.data = options.params ?? {};
    } else if ([RequestType.POST, RequestType.PUT].includes(options.type)) {
      settings.data = options.data ?? {};
    }
    settings.dataType = options.dataType ?? RequestDataType.JSON;
    settings.headers = {
      accept: options.contentType ?? RequestContentType.JSON,
    };
    if (options.skipAuthorization) {
      settings.headers["skip-authorization"] = "on";
    }
    if (options.track) {
      settings.headers["track"] = "on";
    }
    settings.url = resolveUrl(path);

    settings.beforeSend = (xhr) => {
      const token = metadata()?.token;
      if (token) {
        xhr.setRequestHeader("Authorization", `Bearer ${token}`);
      }
      if (window.sessionId) {
        xhr.setRequestHeader("session-id", window.sessionId);
      }
    };
    settings.complete = (XMLHttpRequest, textStatus) => {
      if (["success", "nocontent"].includes(textStatus.toLowerCase())) {
        resolve({
          data: XMLHttpRequest.responseJSON,
          header: XMLHttpRequest.getResponseHeader,
        });
      }
    };
    settings.error = (jqXHR: any, exception: any) => {
      if ([401].includes(jqXHR.status)) {
        if (!options.noLogout) {
          document.location.href = `${metadata()?.url?.self}/Logout`;
        }
      } else if ([200, 204].includes(jqXHR.status)) {
        resolve({
          data: jqXHR.responseText,
          header: jqXHR.getResponseHeader,
        });
      } else {
        let msg = "";
        if (jqXHR.responseJSON && jqXHR.responseJSON.title) {
          msg = jqXHR.responseJSON.title;
        } else if (jqXHR.status === 0) {
          msg = "Not connect, Verify Network.";
        } else if (jqXHR.status == 404) {
          msg = "Requested page not found. [404]";
        } else if (jqXHR.status == 500) {
          msg = "Internal Server Error [500].";
          try {
            const data = JSON.parse(jqXHR.statusText);
            if (data.hasOwnProperty("title")) {
              exception = data.title;
              if (data.hasOwnProperty("details")) {
                msg = Object.keys(data.details)
                  .map((name) => {
                    return `${data.details[name].join(", ")}`;
                  })
                  .join(", ");
              }
            }
          } catch (e) {}
        } else if (exception === "parsererror") {
          msg = "Requested JSON parse failed.";
        } else if (exception === "timeout") {
          msg = "Time out error.";
        } else if (exception === "abort") {
          msg = "Ajax request aborted.";
        } else {
          msg = "Uncaught Error : " + jqXHR.responseText;
        }
        if (!options.silent) {
          apiError({ msg, xhr: jqXHR, exception });
        }
        reject({ msg, xhr: jqXHR, exception });
      }
    };
    const xhr = ajax(settings);
    if (options.request && typeof options.request === "function") {
      options.request(xhr);
    }
  });
}

class Api {
  #silent = false;
  #skipAuthorization = false;
  #track = false;
  #noLogout = false;

  silent() {
    this.#silent = true;
    return this;
  }

  skipAuthorization() {
    this.#skipAuthorization = true;
    return this;
  }

  track() {
    this.#track = true;
    return this;
  }

  noLogout() {
    this.#noLogout = true;
    return this;
  }

  get(path: string, options: any = {}) {
    return request(path, {
      ...options,
      ...{
        type: RequestType.GET,
        silent: this.#silent,
        skipAuthorization: this.#skipAuthorization,
        track: this.#track,
        noLogout: this.#noLogout,
      },
    });
  }

  post(path: string, data: { [key: string]: any }) {
    return request(path, {
      type: RequestType.POST,
      data,
      silent: this.#silent,
      skipAuthorization: this.#skipAuthorization,
      track: this.#track,
      noLogout: this.#noLogout,
    });
  }

  put(path: string, data: { [key: string]: any }) {
    return request(path, {
      type: RequestType.PUT,
      data,
      silent: this.#silent,
      skipAuthorization: this.#skipAuthorization,
      track: this.#track,
      noLogout: this.#noLogout,
    });
  }

  delete(path: string) {
    return request(path, {
      type: RequestType.DELETE,
      silent: this.#silent,
      skipAuthorization: this.#skipAuthorization,
      track: this.#track,
      noLogout: this.#noLogout,
    });
  }
}

export function api() {
  return new Api();
}

export function apiError(data: any) {
  const { msg, xhr, exception } = data;

  let message = `<p>${msg}</p>`;
  if (metadata()?.env === "dev") {
    if (xhr.responseJSON?.message) {
      message += `<p>${xhr.responseJSON?.message}</p>`;
    }
    let text: any[] = [];
    if (xhr.responseJSON?.details) {
      for (const name in xhr.responseJSON.details) {
        text = [...text, ...xhr.responseJSON.details[name]];
      }
    }
    if (text.length > 0) {
      message += `<ul><li>${text.join(`</li><li>`)}</li></ul>`;
    }
  }

  dangerToast(exception.toUpperCase(), message);
}
