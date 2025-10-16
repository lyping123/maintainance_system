<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import InputError from './InputError.vue';

const modelValue = defineModel({ type: [String, Number], required: false });

const props = defineProps({
  id: { type: String, default: undefined },
  name: { type: String, default: undefined },
  options: { type: Array, default: () => [] },
  placeholder: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  error: { type: String, default: '' },
  minChars: { type: Number, default: 1 },
});

const inputRef = ref(null);
const showDropdown = ref(false);
const inputText = ref('');
const highlighted = ref(-1);

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

// Initialize inputText from existing modelValue (try to find matching option label)
onMounted(() => {
  const mv = modelValue?.value;
  if (mv !== undefined && mv !== null && mv !== '') {
    const match = props.options.find(o => String(optionValue(o)) === String(mv));
    if (match) inputText.value = optionLabel(match);
    else inputText.value = String(mv);
  }
});

watch(() => props.options, (n) => {
  // if options change and modelValue matches something, keep inputText label synced
  const mv = modelValue?.value;
  if (mv !== undefined && mv !== null && mv !== '') {
    const match = n.find(o => String(optionValue(o)) === String(mv));
    if (match) inputText.value = optionLabel(match);
  }
});

// Filtered options based on inputText
const filtered = computed(() => {
  const q = String(inputText.value || '').trim().toLowerCase();
  if (!q || q.length < props.minChars) return [];
  return props.options.filter(o => String(optionLabel(o)).toLowerCase().includes(q));
});

function openDropdown() {
  if (props.disabled) return;
  showDropdown.value = filtered.value.length > 0;
}

function closeDropdown() {
  showDropdown.value = false;
  highlighted.value = -1;
}

function chooseOption(opt) {
//   const val = optionValue(opt);
  const label = optionLabel(opt);
  // update model value and input text
  if (modelValue) modelValue.value = label;
  inputText.value = label;
  closeDropdown();
}

function onInput(e) {
  inputText.value = e.target.value;
  if (inputText.value && inputText.value.length >= props.minChars && filtered.value.length > 0) showDropdown.value = true;
  else showDropdown.value = false;
}

function onKeydown(e) {
  if (!showDropdown.value) {
    if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && filtered.value.length > 0) {
      showDropdown.value = true;
      highlighted.value = 0;
      e.preventDefault();
    }
    return;
  }

  if (e.key === 'ArrowDown') {
    highlighted.value = (highlighted.value + 1) % filtered.value.length;
    e.preventDefault();
  } else if (e.key === 'ArrowUp') {
    highlighted.value = (highlighted.value - 1 + filtered.value.length) % filtered.value.length;
    e.preventDefault();
  } else if (e.key === 'Enter') {
    const opt = filtered.value[highlighted.value] ?? filtered.value[0];
    if (opt) chooseOption(opt);
    e.preventDefault();
  } else if (e.key === 'Escape') {
    closeDropdown();
  }
}

// sync external modelValue changes to inputText when possible
// sync external modelValue changes to inputText when possible
watch(() => modelValue?.value, (nv) => {
  if (nv === undefined || nv === null || nv === '') return;
  const match = props.options.find(o => String(optionValue(o)) === String(nv));
  if (match) inputText.value = optionLabel(match);
});

defineExpose({ focus: () => inputRef.value?.focus() });
</script>

<template>
  <div class="relative w-full">
    <input
      :id="id"
      :name="name"
      ref="inputRef"
      :disabled="disabled"
      :placeholder="placeholder"
      class="w-full rounded-md border-gray-300 shadow-sm p-2 focus:border-indigo-500 focus:ring-indigo-500"
      v-model="inputText"
      @input="onInput"
      @focus="openDropdown"
      @keydown="onKeydown($event)"
      autocomplete="off"
    />
<!-- @blur="setTimeout(() => closeDropdown(), 150)" -->
    <ul v-if="showDropdown && filtered.length > 0" class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-auto bg-white border rounded shadow-lg">
      <li
        v-for="(opt, idx) in filtered"
        :key="idx"
        @mousedown.prevent="chooseOption(opt)"
        :class="['px-3 py-2 cursor-pointer hover:bg-gray-100', highlighted === idx ? 'bg-blue-50' : '']"
      >
        {{ optionLabel(opt) }}
      </li>
    </ul>

    <InputError :message="error" />
  </div>
</template>

<style scoped>
/* small tweak to ensure list appears above other elements */
.autocomplete-list {
  z-index: 9999;
}
</style>
