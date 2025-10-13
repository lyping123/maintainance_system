<script setup>
import { ref, onMounted } from 'vue';

const modelValue = defineModel({ type: String, required: true });
const inputRef = ref(null);

onMounted(() => {
    if (inputRef.value?.hasAttribute('autofocus')) {
        inputRef.value.focus();
    }
});

defineExpose({
    focus: () => inputRef.value?.focus()
});
</script>

<template>
    <textarea
        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        v-model="modelValue"
        ref="inputRef"
        rows="4"
    />
</template>
