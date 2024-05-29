export function getDaysCount(month: number, year: number) {
	if (month < 0) {
		month = 11
		year--
	} else if (month > 11) {
		month = 0
		year++
	}
	return 32 - new Date(year, month, 32).getDate()
}

export function getFirstDay(month: number, year: number) {
	let d = new Date(year, month, 1)
	let i = d.getDay() - 1
	if (i < 0) {
		i = 6
	}
	return i
}

export function calculateDays(month: number, year: number, selectedDate) {
	const today: Date = new Date()
	const daysList: any = []
	const days: number = getDaysCount(month, year)
	const firstDay: number = getFirstDay(month, year)
	const prevDays: number = getDaysCount(month - 1, year)
	let count: number = 0

	// previous
	let toStart = prevDays - firstDay
	for (let i = 0; i < firstDay; i++) {
		toStart++
		let date = getDate(year, month, toStart, "--")
		daysList.push({
			text: toStart,
			today: date.toDateString() === today.toDateString(),
			type: "previous",
			date: getDateFormat(date),
			day: date.getDate(),
			active: isActiveDate(date, selectedDate),
		})
		count++
	}

	// current
	for (let i = 1; i <= days; i++) {
		let date = getDate(year, month, i)
		daysList.push({
			text: i,
			today: date.toDateString() === today.toDateString(),
			type: "current",
			date: getDateFormat(date),
			day: date.getDate(),
			active: isActiveDate(date, selectedDate),
		})
		count++
	}

	// next
	let toEnd = 42 - count
	for (let i = 0; i < toEnd; i++) {
		let date = getDate(year, month, i + 1, "++")
		daysList.push({
			text: i + 1,
			today: date.toDateString() === today.toDateString(),
			type: "next",
			date: getDateFormat(date),
			day: date.getDate(),
			active: isActiveDate(date, selectedDate),
		})
	}

	return daysList
}

export function getDate(
	year: number,
	month: number,
	day: number,
	operation: string = "",
) {
	if (operation === "--") {
		month--
	} else if (operation === "++") {
		month++
	}
	if (month < 0) {
		month = 11
		year--
	} else if (month > 11) {
		month = 0
		year++
	}
	return new Date(year, month, day)
}

export function getDateFormat(date: Date) {
	return `${date.getFullYear()}-${(date.getMonth() + 1)
		.toString()
		.padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}`
}

export function isActiveDate(date: Date, selectedDate) {
	return (
		date.getFullYear() === selectedDate.getFullYear() &&
		date.getMonth() === selectedDate.getMonth() &&
		date.getDate() === selectedDate.getDate()
	)
}

export function daysToRows(array: any) {
	let i: number,
		j: number,
		list: any = [],
		chunk: number = 7
	for (i = 0, j = array.length; i < j; i += chunk) {
		list.push(array.slice(i, i + chunk))
	}
	return list
}
