<script setup lang="ts">
import { api } from '@/lib/api';
import { confirm } from '@/modules/bootstrap/lib/confirm';
import { dangerToast } from '@/modules/bootstrap/lib/toast';
import Icon from '@/components/Icon.vue';
import { mdiDelete, mdiEye, mdiPlusThick } from '@mdi/js';
import { ref } from 'vue';

interface Props {
    section?: string,
    accept?: Array<string> | string
    url?: any | null
}

const props = withDefaults(defineProps<Props>(), {
    section: 'files',
    accept: "*",
    url: null,
});

const file = ref<any>(props.url || null)
const uploading = ref<boolean>(false)

const emit = defineEmits(['file', 'add', 'remove', 'done', 'view', 'cancel'])

const loadFiles = (e: any) => {
    const files = e.target.files
    if (files.length > 0) {
        loadFile(files[0])
    } else {
        emit('cancel')
    }
}

const loadFile = (file: File) => {
    emit('add', 'file', file)
    const reader = new FileReader()
    reader.onload = (e: any) => {
        const base64 = e.target.result
        emit('add', 'base64', base64)
        uploadToServer(base64, file.name)
    }
    reader.onerror = () => {
        console.error(`Error occurred reading file: ${file.name}`)
    }
    reader.readAsDataURL(file)
}

const uploadToServer = (base64: string, name: string) => {
    if (base64) {
        uploading.value = true
        file.value = base64
        api().post('/media/file', { section: props.section, base64, name }).then(({ data }: any) => {
            data = { ...data }
            file.value = data
            emit('file', data)
        }).finally(() => {
            uploading.value = false
            emit('done', base64)
        })
    } else {
        dangerToast('Something went wrong')
    }
}

const open = () => {
    if (file.value) {
        window.open(file.value.url, '_blank');
    }
}

const remove = () => {
    confirm("Remove File").then((confirmed: boolean) => {
        if (confirmed) {
            file.value = null
            emit('file', null)
            emit('remove')
        }
    })
}
</script>
<template>
    <div class="select-file-component">
        <template v-if="file">
            <div
                class="file-output"
            >
                <div :class="{ icon: true, [file.extension]: true }">
                    <div class="extension">{{ file.extension }}</div>
                    <div class="mime">{{ file.mime }}</div>
                    <div class="size">{{ file.size }}</div>
                </div>
                <div class="name">{{ file.name }}</div>
            </div>
            <div
                v-if="uploading"
                class="uploading"
            >
                <div
                    class="spinner-border text-primary"
                    role="status"
                    style="width: 3rem; height: 3rem;"
                >
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div
                v-if="!uploading"
                class="actions"
            >
                <button
                    type="button"
                    class="btn btn-primary btn-sm"
                    @click="open"
                >
                    <Icon :path="mdiEye" />
                </button>
                <button
                    type="button"
                    class="btn btn-danger btn-sm"
                    @click="remove"
                >
                    <Icon :path="mdiDelete" />
                </button>
            </div>
        </template>
        <template v-else>
            <label class="file-input">
                <input
                    type="file"
                    @input="loadFiles"
                    class="file-input"
                    :accept="Array.isArray(accept) ? accept.join(',') : accept"
                >
                <slot name="add">
                    <div class="add-btn">
                        <Icon :path="mdiPlusThick" />
                    </div>
                </slot>
            </label>
        </template>
    </div>
</template>
<style scoped lang="scss">
@import "@scss/bootstrap";
@import "@scss/mixins/font-size";

.select-file-component {
    position: relative;
    width: 200px;
    padding-top: 200px;
    background: $gray-1;
    border: 1px solid $gray-2;
    background: $white;
    border-radius: 10px;
    user-select: none;
    overflow: hidden;

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        cursor: pointer;

        input[type=file] {
            position: absolute;
            left: -200vw;
        }

        .add-btn {
            flex: auto;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            @include font-size(5);
        }
    }

    .file-output {
        $color: $blue;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        cursor: pointer;

        .icon {
            width: 100%;
            height: 140px;
            position: relative;

            &.pdf {
                $color: $red-6;
            }

            background: $color;

            .extension {
                position: relative;
                color: $white;
                flex: auto;
                height: 100%;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
                @include font-size(2.5);
            }

            .size {
                position: absolute;
                bottom: 5px;
                left: 5px;
                display: flex;
                flex-direction: row;
                @include font-size(.7);
                background: $white;
                padding: 1px 5px;
                border-radius: 5px;
            }

            .mime {
                position: absolute;
                bottom: 5px;
                right: 5px;
                display: flex;
                flex-direction: row;
                @include font-size(.7);
                background: $white;
                padding: 1px 5px;
                border-radius: 5px;
            }
        }

        .name {
            flex: auto;
            position: relative;
            margin-top: auto;
            white-space: wrap;
            @include font-size(.8);
            color: $white;
            background: darken($color, 10%);
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            overflow-wrap: anywhere;
            text-align: center;
            padding: 0 10px;
            width: 100%;
        }
    }

    .uploading,
    .actions {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        background: rgba(255, 255, 255, 0.6);
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }

    .actions {
        gap: 10px;
        transition: all 0.25s ease;
        transform: translateY(100vh);
    }

    &:hover {
        .actions {
            transform: translateY(0);
        }
    }
}
</style>
