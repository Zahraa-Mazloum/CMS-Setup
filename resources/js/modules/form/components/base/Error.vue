<script lang="ts" setup>
import { Field } from "../../lib/field";

defineOptions({
  inheritAttrs: false,
});

type Props = {
  field: Field;
};

const { field } = defineProps<Props>();
</script>
<template>
    <div
        v-if="field.hasErrors"
        class="invalid-feedback d-block"
        v-bind="$attrs"
    >
        <ul v-if="field.errors.length > 1">
            <li
                v-for="(error, i) in field.errors"
                :key="i"
            >{{ error }}</li>
        </ul>
        <template v-else>{{ field.errors.join("") }}</template>
    </div>
</template>
<style scoped lang="scss">

</style>
