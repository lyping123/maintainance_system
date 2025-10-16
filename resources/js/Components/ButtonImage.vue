<script setup>
import { ref, computed } from 'vue';
import Modal from './Modal.vue';

const props = defineProps({
    images: { type: Array, default: () => [] }, // items can be strings (urls) or { url, caption }
    thumbSize: { type: Number, default: 96 },
});

const showModal = ref(false);
const currentIndex = ref(0);

function openAt(i) {
    currentIndex.value = i;
    showModal.value = true;
}

function prev() {
    if (currentIndex.value > 0) currentIndex.value -= 1;
}

function next() {
    if (currentIndex.value < props.images.length - 1) currentIndex.value += 1;
}

const normalized = computed(() => props.images.map(i => (typeof i === 'string' ? { url: "/storage/"+i, caption: '' } : i)));
</script>

<template>
    <div>
        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
            <div v-for="(img, idx) in normalized" :key="idx" class="cursor-pointer">
                <!-- <img :src="img.url" :alt="img.caption || `Image ${idx+1}`" :style="{ width: thumbSize + 'px', height: thumbSize + 'px', objectFit: 'cover' }" @click="openAt(idx)" class="rounded-md shadow-sm" />
                  -->
                <button :class="{'border-2 border-gray-300': idx !== currentIndex }" @click="openAt(idx)" class="rounded-md shadow-sm">View attachment</button>
            </div>
        </div>

        <Modal :show="showModal" @close="showModal = false">
            <div class="max-w-4xl mx-auto">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-700">{{ currentIndex + 1 }} / {{ normalized.length }}</div>
                    <div class="flex items-center space-x-2">
                        <button @click="prev" :disabled="currentIndex===0" class="px-3 py-1 bg-gray-100 rounded">Prev</button>
                        <button @click="next" :disabled="currentIndex>=normalized.length-1" class="px-3 py-1 bg-gray-100 rounded">Next</button>
                    </div>
                </div>

                <div class="bg-black/75 p-4 flex justify-center">
                    <img :src="normalized[currentIndex].url" class="max-h-[70vh] max-w-full object-contain" />
                </div>

                <div class="mt-2 text-sm text-gray-200 text-center">{{ normalized[currentIndex].caption }}</div>
            </div>
        </Modal>
    </div>
</template>
