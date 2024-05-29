import { ref, Ref } from "vue";

type SlotRef = [Ref<any>, (el: any) => void]

export const createSlotRef = (): SlotRef => {

    const element = ref<any>(null)

    const setElement = (el: any) => {
        element.value = el
    }

    return [element, setElement];
}
