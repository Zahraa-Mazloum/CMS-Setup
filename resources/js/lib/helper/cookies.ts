export function getCookie(name): any {
  var v = document.cookie.match("(^|;) ?" + name + "=([^;]*)(;|$)");
  return v ? v[2] : null;
}

export function setCookie(name, value, days): void {
  var d = new Date();
  d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
  document.cookie = name + "=" + value + ";path=/;expires=" + d.toString();
}

export function deleteCookie(name): void {
  setCookie(name, "", -1);
}
