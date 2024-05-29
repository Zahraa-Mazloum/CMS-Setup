import { createPrivateField } from "@/lib/private"

const [active, setActive] = createPrivateField<EventDispatcher, boolean>(true)
const [events, setEvents] = createPrivateField<
	EventDispatcher,
	EventListen[]
>([])

type EventListen<Args extends {} = {}> = (params: { [name in keyof Args]?: Args[name] }) => void

export class EventDispatcher<Args extends {} = {}> {
	constructor(public name: string, public sender: any) {
		setActive(this, true)
		setEvents(this, [])
	}

	listen(callback: EventListen<Args>) {
		setEvents(this, (events: EventListen<Args>[]) => {
			events.push(callback)
            return events
		})
	}

	dispatch(params: { [name in keyof Args]?: Args[name] } = {}) {
		if (active(this)) {
			events(this).forEach((callback: EventListen<Args>) => {
				callback(params)
			})
		}
	}

	activate() {
		setActive(this, true)
	}

	deactivate() {
		setActive(this, false)
	}
}
