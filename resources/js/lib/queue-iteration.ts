class QueueIteration {
	private _action: (index: number, next: () => void) => void = () => {}
	private _events: any = {}

	constructor(public count: number) {}

	set action(callback: (index: number, next: () => void) => void) {
		this._action = callback
	}

	get action(): (index: number, next: () => void) => void {
		return this._action
	}

	on(eventName, callback) {
		if (!this._events.hasOwnProperty(eventName)) {
			this._events[eventName] = []
		}
		this._events[eventName].push(callback)
	}

	dispatch(eventName, data: unknown = null) {
		if (this._events.hasOwnProperty(eventName)) {
			for (const event of this._events[eventName]) {
				event(
					new CustomEvent(eventName, {
						bubbles: true,
						detail: {
							target: this,
							data,
						},
					}),
				)
			}
		}
	}

	start() {
		this.dispatch("start")
		execute(this)
	}
}

function execute(queueIteration: QueueIteration, i: number = 0) {
	progress(queueIteration, i)
	if (queueIteration.count > i) {
		queueIteration.action(i, () => {
			execute(queueIteration, i + 1)
		})
	} else {
		queueIteration.dispatch("done")
	}
}

function progress(queueIteration: QueueIteration, current: number) {
	let percentage = Math.ceil((current * 100) / queueIteration.count)
	if (percentage > 100) {
		percentage = 100
	}
	queueIteration.dispatch("progress", {
		current,
		total: queueIteration.count,
		percentage: percentage,
	})
}

export function createQueueIteration(count: number) {
	return new QueueIteration(count)
}
