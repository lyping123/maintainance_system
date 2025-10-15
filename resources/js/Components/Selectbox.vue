<script setup>
import { ref, onMounted } from 'vue';
import InputError from './InputError.vue';

const modelValue = defineModel({ type: [String, Number], required: false });

const props = defineProps({
	id: { type: String, default: undefined },
	name: { type: String, default: undefined },
	options: { type: Array, default: () => [] },
	placeholder: { type: String, default: '' },
	disabled: { type: Boolean, default: false },
	error: { type: String, default: '' },
});

const selectRef = ref(null);

onMounted(() => {
	if (selectRef.value?.hasAttribute('autofocus')) {
		selectRef.value.focus();
	}
});

defineExpose({ focus: () => selectRef.value?.focus() });

function optionValue(opt) {
	if (opt === null || opt === undefined) return '';

	if (typeof opt === 'object') return opt.value ?? opt.id ?? opt.key ?? '';
	return opt;
}

function optionLabel(opt) {
	if (opt === null || opt === undefined) return '';
	if (typeof opt === 'object') return opt.label ?? opt.name ?? opt.text ?? String(opt.value ?? opt.id ?? opt.key ?? '');
	return String(opt);
}
</script>

<template>
	<div>
		<select
			:id="id"
			:name="name"
			v-model="modelValue"
			ref="selectRef"
			:disabled="disabled"
			class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full p-2"
		>
			<option v-if="placeholder" value="">{{ placeholder }}</option>
			<option v-for="(opt, idx) in options" :key="idx" :value="optionValue(opt)">
				{{ optionLabel(opt) }}
			</option>
		</select>

		<InputError :message="error" />
	</div>
</template>

