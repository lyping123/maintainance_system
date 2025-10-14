<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Textarea from '@/Components/Textarea.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

import { ref } from 'vue';

const props = defineProps({
    reportDetail: Object,
});

// Form used for assigning person in charge and saving solution
const form = useForm({
    id: null,
    person_in_charge: '',
    solution: '',
});

const showAssignModal = ref(false);
const showSolutionModal = ref(false);

function openAssign() {
    form.person_in_charge = props.report?.person_in_charge || '';
    form.id = props.report?.id || null;
    showAssignModal.value = true;
}

function openSolution() {
    form.solution = props.report?.solution || '';
    form.id = props.report?.id || null;
    showSolutionModal.value = true;
}

const closeModal = () => {
    showAssignModal.value = false;
    showSolutionModal.value = false;
    form.reset();
    form.clearErrors();
};

function submitAssign() {
    const id = form.id;
    form.put(route('report.assign', { id: id }), {
        preserveScroll: true,
        onFinish: closeModal,
    });
}

function submitSolution() {
    const id = form.id;
    form.put(route('report.solve', { id: id }), {
        preserveScroll: true,
        onFinish: closeModal,
    });
}

function backToList() {
    router.get(route('reports.index'));
}

</script>

<template>
    <Head title="Report Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 ">Report Details</h2>
            </div>
        </template>
    

        <div class="space-y-6">
            <!-- Issue panel -->
            <div class="bg-yellow-200 rounded-lg shadow p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ reportDetail.report_type }} - {{ reportDetail.places }}</h3>
                        <p class="text-sm text-gray-500">Reported by: <span class="font-medium text-gray-700">{{ reportDetail.s_name }}</span></p>
                        <p class="mt-3 text-gray-700">{{ reportDetail.report_issue }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Status</div>
                        <div class="mt-1">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-xs font-semibold',
                                    reportDetail.status === 'Completed' ? 'bg-green-100 text-green-800' :
                                    reportDetail.status === 'Pending' ? 'bg-yellow-100 text-yellow-800' :
                                    reportDetail.status === 'Overdue' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
                                ]"
                            >
                                {{ reportDetail.status }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">{{ reportDetail.created_date }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Person in charge panel -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-md font-semibold text-gray-800">Person In Charge</h4>
                            <p class="text-sm text-gray-600 mt-2">{{ reportDetail.person_in_charge || 'Not assigned' }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <SecondaryButton @click="openAssign">Assign / Edit</SecondaryButton>
                        </div>
                    </div>
                </div>

                <!-- Solution panel -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-md font-semibold text-gray-800">Solution</h4>
                            <p class="text-sm text-gray-600 mt-2" v-if="reportDetail.solution">{{ reportDetail.solution }}</p>
                            <p class="text-sm text-gray-500 mt-2 italic" v-else>No solution recorded yet.</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <DangerButton @click="openSolution">Add / Update</DangerButton>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logs or progress (if any) -->
            <!-- <div class="bg-white rounded-lg shadow p-6">
                <h4 class="text-md font-semibold text-gray-800">Progress / Notes</h4>
                <div class="mt-4 text-sm text-gray-600">
                    <template v-if="report.progress && report.progress.length">
                        <ul class="space-y-3">
                            <li v-for="p in report.progress" :key="p.id" class="border rounded p-3">
                                <div class="text-xs text-gray-500">{{ p.created_at }}</div>
                                <div class="text-sm text-gray-700">{{ p.note }}</div>
                            </li>
                        </ul>
                    </template>
                    <p v-else class="text-sm text-gray-500">No progress notes available.</p>
                </div>
            </div> -->
        </div>

        <!-- Assign Modal -->
        <Modal :show="showAssignModal" @close="closeModal">
            <div class="p-6 bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
                <h3 class="text-lg font-semibold mb-4">Assign Person In Charge</h3>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="person_in_charge" value="Person In Charge" />
                        <TextInput id="person_in_charge" v-model="form.person_in_charge" type="text" class="mt-1 block w-full" placeholder="Name of person responsible" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton @click="submitAssign">Save</DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Solution Modal -->
        <Modal :show="showSolutionModal" @close="closeModal">
            <div class="p-6 bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto">
                <h3 class="text-lg font-semibold mb-4">Add / Update Solution</h3>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="solution" value="Solution" />
                        <Textarea id="solution" v-model="form.solution" rows="4" class="mt-1 block w-full" placeholder="Describe the solution taken" />
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton @click="submitSolution">Save Solution</DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>