import { EventDispatcher } from "@/lib/event-dispatcher";

export class Base<Events extends {} = {}> {
  events: Events;

  constructor(private _baseName?: string) {
    if (!_baseName) {
      this._baseName = this.constructor.name;
    }
    this.events = this.defineEvents();
    this.init();
  }

  init() {}

  get className(): string {
    return this._baseName as string;
  }

  protected set className(name: string) {
    this._baseName = name;
  }

  protected defineEvents(): Events {
    return new Proxy<Events>({} as Events, {
      get: (target: any, property: string, receiver: any) => {
        if (!target.hasOwnProperty(property)) {
          Reflect.set(
            target,
            property,
            new EventDispatcher(property, this),
            receiver
          );
        }
        return Reflect.get(target, property, receiver);
      },
    });
  }
}
