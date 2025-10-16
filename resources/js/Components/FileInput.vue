<script setup>
import { ref, onMounted, computed, watch, onBeforeUnmount } from 'vue';
import InputLabel from './InputLabel.vue';
import InputError from './InputError.vue';

// v-model support (File | Array<File> or null)
const modelValue = defineModel({ type: [Object, Array], required: false });

const props = defineProps({
    id: { type: String, default: undefined },
    name: { type: String, default: undefined },
    accept: { type: String, default: '' },
    multiple: { type: Boolean, default: false },
    error: { type: String, default: '' },
    placeholder: { type: String, default: 'Choose file...' },
    images: {type: String,default:''}
});

const inputRef = ref(null);

onMounted(() => {
    if (inputRef.value?.hasAttribute('autofocus')) {
        inputRef.value.focus();
    }
    
});

defineExpose({ focus: () => inputRef.value?.focus() });

function handleChange(e) {
    const files = e.target.files;
    if (!files) {
        // clear
        if (props.multiple) modelValue.value = [];
        else modelValue.value = null;
        return;
    }

    if (props.multiple) {
        modelValue.value = Array.from(files);
    } else {
        modelValue.value = files[0] || null;
    }
}

const selectedNames = computed(() => {
    if (props.multiple) {
        const arr = modelValue.value || [];
        return Array.isArray(arr) ? arr.map(f => f.name).join(', ') : '';
    }
    const f = modelValue.value;
    return f ? (f.name || String(f)) : '';
});

// previews for image files
const previews = ref([]);

function generatePreviews(files) {
    // cleanup old
    
    previews.value.forEach(p => URL.revokeObjectURL(p.url));
    
    previews.value = [];

    const arr = Array.isArray(files) ? files : (files ? [files] : []);
    arr.forEach(f => {
        if (!f || !f.type) return;
        if (f.type.startsWith('image/')) {
            const url = URL.createObjectURL(f);
            previews.value.push({ name: f.name, url, file: f });
        }
    });
}

watch(() => modelValue.value, (nv) => {
    generatePreviews(nv);
}, { immediate: true });

onBeforeUnmount(() => {
    previews.value.forEach(p => URL.revokeObjectURL(p.url));
});

function removeFileAt(index) {
    if (!props.multiple) {
        modelValue.value = null;
        previews.value.forEach(p => URL.revokeObjectURL(p.url));
        previews.value = [];
        inputRef.value.value = null;
        return;
    }

    const arr = Array.isArray(modelValue.value) ? [...modelValue.value] : [];
    const [removed] = arr.splice(index, 1);
    if (removed) URL.revokeObjectURL(previews.value[index]?.url);
    previews.value.splice(index, 1);
    modelValue.value = arr;
}
</script>

<template>
    <div>
        <InputLabel v-if="$slots.label" ><slot name="label" /></InputLabel>
        <label class="block w-full">
            <div class="flex items-center space-x-3">
                <input
                    :id="id"
                    :name="name"
                    ref="inputRef"
                    type="file"
                    :accept="accept"
                    :multiple="multiple"
                    @change="handleChange"
                    class="hidden"
                />

                <button type="button" @click="$refs.inputRef.click()" class="px-3 py-2 bg-gray-100 rounded-md border border-gray-300 text-sm hover:bg-gray-200">
                    {{ placeholder }}
                </button>

                <div class="text-sm text-gray-700 truncate" style="max-width:40ch;">{{ selectedNames }}</div>
            </div>
        </label>

        <div class="mt-3 flex flex-wrap gap-3">
            <template v-for="(p, idx) in previews" :key="p.url">
                <div class="border rounded overflow-hidden w-28">
                    <img :src="p.url" class="w-28 h-20 object-cover" />
                    <div class="flex items-center justify-between px-2 py-1 bg-white">
                        <div class="text-xs truncate">{{ p.name }}</div>
                        <button type="button" @click="removeFileAt(idx)" class="text-red-500 text-xs ml-2">×</button>
                    </div>
                </div>
            </template>
        </div>

        <InputError :message="props.error" />
    </div>
</template>
