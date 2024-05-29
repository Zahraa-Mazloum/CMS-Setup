export function datesDiff(date1: Date, date2: Date) {
    let diff = Math.abs(date1.getTime() - date2.getTime()) / 1000;
    const periods: any = {
      years: 31556926,
      months: 2629743,
      weeks: 604800,
      days: 86400,
      hours: 3600,
      minutes: 60,
      seconds: 1,
    };
    const out: any = [];
    for (let name in periods) {
      const value = Math.floor(diff / periods[name]);
      if (value > 0) {
        diff = diff % periods[name];
        out.push(`${value} ${name}`);
      }
    }
    return out.join(", ");
  }
  