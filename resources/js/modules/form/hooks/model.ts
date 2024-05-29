import { computed, ComputedRef, getCurrentInstance, ref, shallowRef } from "vue"
import { DataList, Model } from '../lib/model';

export interface IConstructor<T extends Model> {
	new (initData: DataList, init: (self: T) => void): T
}

export function createModel<T extends Model>(ModelClass: IConstructor<T>) {
	type Model<T> = ComputedRef<T | null>
	type NewModel = (initData?: DataList, init?: (self: T) => void) => void
	type DestroyModel = () => void

	const instance = getCurrentInstance()

	let modelRef: any = ref<T | null>(null)

	const newModel = (initData: DataList = {}, init: (self: T) => void = () => {}) => {
		const _model = new ModelClass(initData, init)

		_model.events.afterValidate.listen(({ sender }) => {
			instance?.proxy?.$forceUpdate()
		})

		modelRef.value = () => _model
	}

	const destroyModel = () => {
		modelRef.value = null
	}

	const model = computed<T | null>(() => {
		return modelRef.value ? (modelRef.value() as T) : null
	})

	const out: [Model<T>, NewModel, DestroyModel] = [
		model,
		newModel,
		destroyModel,
	]

	return out
}
