import RepeaterInput from "@/modules/form/components/RepeaterInput.vue";
import StringInput from "@/modules/form/components/StringInput.vue";
import TextInput from "@/modules/form/components/TextInput.vue";
import RadioInput from "@/modules/form/components/RadioInput.vue";
import CheckInput from "@/modules/form/components/checkInput.vue";
import DateInput from "@/modules/calendar/components/DateInput.vue";
import ImageInput from "@/modules/upload/components/form/ImageInput.vue";
import FileInput from "@/modules/upload/components/form/FileInput.vue";
import RangeInput from "@/modules/form/components/RangeInput.vue";
import SelectInput from "@/modules/form/components/SelectInput.vue";
import ToggleInput from "@/modules/form/components/ToggleInput.vue";
import GalleryInput from "@/modules/upload/components/form/GalleryInput.vue";
import TagsInput from "@/modules/form/components/TagsInput.vue";
import EditorInput from "@/modules/editor/components/EditorInput.vue";
import PhoneInput from "@/modules/form/components/PhoneInput.vue";
import RecaptchaInput from "../components/RecaptchaInput.vue";

export const fields: { [name: string]: any } = {
  string: StringInput,
  text: TextInput,
  radio: RadioInput,
  checkbox: CheckInput,
  date: DateInput,
  image: ImageInput,
  file: FileInput,
  range: RangeInput,
  select: SelectInput,
  toggle: ToggleInput,
  gallery: GalleryInput,
  tags: TagsInput,
  editor: EditorInput,
  repeater: RepeaterInput,
  phone: PhoneInput,
  recaptcha: RecaptchaInput,
};
