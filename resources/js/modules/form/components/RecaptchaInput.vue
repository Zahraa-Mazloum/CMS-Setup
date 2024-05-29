<script lang="ts">
declare var window: any;

const load = () => {
    const url = 'https://www.google.com/recaptcha/api.js';
    let script: any = document.querySelector(`script[src="${url}"]`);
    if (!script) {
        script = document.createElement("script");
        script.async = true;
        script.src = url;
        document.head.appendChild(script);
    }

    if (typeof window.grecaptcha === 'undefined') {
        window.grecaptcha = {};
    }

    window.grecaptcha.ready = (cb) => {
        if (typeof window.grecaptcha === 'undefined') {
            // window.__grecaptcha_cfg is a global variable that stores reCAPTCHA's
            // configuration. By default, any functions listed in its 'fns' property
            // are automatically executed when reCAPTCHA loads.
            const c = '___grecaptcha_cfg';
            window[c] = window[c] || {};
            (window[c]['fns'] = window[c]['fns'] || []).push(cb);
        } else {
            cb();
        }
    }
}

load();
</script>
<script setup lang="ts">
import { isDark } from '@/modules/theme';
import { onMounted, ref } from 'vue';
import { Field } from '../lib/field';
import { RecaptchaRule } from '../rules/recaptcha';
import FieldWrapper, { GroupSlots } from './base/FieldWrapper.vue';
import { metadata } from '../../../lib/metadata';

defineOptions({
    inheritAttrs: false,
});

defineSlots<GroupSlots>();

type Props = {
    field: Field;
};
const props = defineProps<Props>();

const { field } = props;

const container = ref<any>(null);
const recaptcha = ref<any>(null);

onMounted(() => {
    window.grecaptcha?.ready?.(() => {
        recaptcha.value = window.grecaptcha?.render?.(container.value, {
            sitekey: metadata()?.recaptchaSiteKey,
            theme: isDark() ? 'dark' : 'light',
        });
        const rule = RecaptchaRule.assign(field);
        field.parent.validationRules.push(rule);
        rule.widget = recaptcha.value
    });
})

</script>
<template>
    <FieldWrapper :field="field" no-label>
        <template v-for="(_, slot) in $slots" v-slot:[slot]="scope">
            <slot :name="slot" v-bind="{ ...scope }" />
        </template>
        <template #="{ id }">
            <div :id="id" ref="container"></div>
        </template>
    </FieldWrapper>
</template>
<style lang="scss">
iframe[title=reCAPTCHA] {
    width: 302px;
    height: 76px;
    border-radius: 3px;
}
</style>
