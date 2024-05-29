const Box = require("cli-box");

export const box = (text) => {
  const lines = text.split("\n");
  let max = 0;
  lines.forEach((line) => {
    if (line.length > max) {
      max = line.length;
    }
  });
  return new Box(
    {
      w: max,
      h: lines.length,
      stringify: false,
      marks: {
        nw: "╭",
        n: "─",
        ne: "╮",
        e: "│",
        se: "╯",
        s: "─",
        sw: "╰",
        w: "│",
      },
      hAlign: "left",
    },
    text
  ).stringify();
};
