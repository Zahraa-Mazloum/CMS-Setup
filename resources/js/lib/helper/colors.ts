export const rgbToHex = (r: number, g: number, b: number) => {
    return "#" + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1);
  };
  
  export const hexToRgb = (hex: string) => {
    hex = hex.replace(/#/g, "");
    if (hex.length === 3) {
      hex = hex
        .split("")
        .map(function (hex) {
          return hex + hex;
        })
        .join("");
    }
    // validate hex format
    let result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})[\da-z]{0,0}$/i.exec(hex);
    if (result) {
      let red = parseInt(result[1], 16);
      let green = parseInt(result[2], 16);
      let blue = parseInt(result[3], 16);
  
      return [red, green, blue];
    } else {
      // invalid color
      return null;
    }
  };
  
  export const cssHexToRgb = (hex: string, alpha: number = 1) => {
    const c = hexToRgb(hex);
    if (Array.isArray(c)) {
      return `rgba(${c[0]},${c[1]},${c[2]},${alpha})`;
    }
    return hex;
  };
  
  export const hexToAlbedo = (hex: string) => {
    const [r, g, b]: any = hexToRgb(hex);
    return rgbToAlbedo(r, g, b);
  };
  
  export const rgbToAlbedo = (r: number, g: number, b: number) => {
    return [
      Math.pow(r / 255, 2.2),
      Math.pow(g / 255, 2.2),
      Math.pow(b / 255, 2.2),
    ];
  };
  
  export const albedoToRgb = (albedo) => {
    const [r, g, b]: any = albedo;
    return [
      Math.pow(r, 1 / 2.2) * 255,
      Math.pow(g, 1 / 2.2) * 255,
      Math.pow(b, 1 / 2.2) * 255,
    ];
  };
  
  export const albedoToHex = (albedo) => {
    const [r, g, b] = albedoToRgb(albedo);
    return rgbToHex(r, g, b);
  };
  
  export const rgbToHsb = (r: number, g: number, b: number) => {
    r /= 255;
    g /= 255;
    b /= 255;
    const v = Math.max(r, g, b),
      n = v - Math.min(r, g, b);
    const h =
      n === 0
        ? 0
        : n && v === r
        ? (g - b) / n
        : v === g
        ? 2 + (b - r) / n
        : 4 + (r - g) / n;
    return [60 * (h < 0 ? h + 6 : h), v && (n / v) * 100, v * 100];
  };
  
  export const hexToHsl = (hex: string) => {
    hex = hex.replace(/#/g, "");
    if (hex.length === 3) {
      hex = hex
        .split("")
        .map(function (hex) {
          return hex + hex;
        })
        .join("");
    }
    let result = /^([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})[\da-z]{0,0}$/i.exec(hex);
    if (!result) {
      return null;
    }
    let r = parseInt(result[1], 16);
    let g = parseInt(result[2], 16);
    let b = parseInt(result[3], 16);
    (r /= 255), (g /= 255), (b /= 255);
    let max = Math.max(r, g, b),
      min = Math.min(r, g, b);
    let h,
      s,
      l = (max + min) / 2;
    if (max == min) {
      h = s = 0;
    } else {
      let d = max - min;
      s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
      switch (max) {
        case r:
          h = (g - b) / d + (g < b ? 6 : 0);
          break;
        case g:
          h = (b - r) / d + 2;
          break;
        case b:
          h = (r - g) / d + 4;
          break;
      }
      h /= 6;
    }
    s = s * 100;
    s = Math.round(s);
    l = l * 100;
    l = Math.round(l);
    h = Math.round(360 * h);
  
    return [h, s, l];
  };
