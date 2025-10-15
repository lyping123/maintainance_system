<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Textarea from '@/Components/Textarea.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed, ref } from 'vue';

import Selectbox from '@/Components/Selectbox.vue';


const props = defineProps({
    reportDetail: Object,
    techbicalPerson:Object,
    filters: Object,
});

const form = useForm({
    id: null,
    p_id: 0,
    solution: '',
});


const selectedField = ref(props.filters?.field || '');
const filterOption = computed(() => {
    const map = new Map();
    (props.techbicalPerson || []).forEach(p => {
        if (p && (p.id !== undefined) && p.name) {
            if (p.field) {
                map.set(p.id, `${p.name}   (${p.field})`);
            } else {
                map.set(p.id, p.name);
            }
        }
    });
    const arr = Array.from(map, ([id, name]) => ({ label: name, value: id }));
    return arr.sort((a, b) => String(a.label).localeCompare(String(b.label)));
});


// Form used for assigning person in charge and saving solution


console.log(props.reportDetail.s_name);
const showAssignModal = ref(false);
const showSolutionModal = ref(false);
const editingAssignmentId = ref(null);


function openAssign() {
    // Open modal to add new assignment
    editingAssignmentId.value = null;
    form.p_id = 0;
    form.solution = '';
    form.id = props.reportDetail?.id || null;
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
    const id = props.reportDetail.id;
    form.p_id = selectedField.value;

    if (!editingAssignmentId.value) {
        // create
        form.post(route('report.assign.technician', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Assignment added successfully.');
            },
            onFinish: closeModal,
        });
    } else {
        // update existing assignment
        const assignmentId = editingAssignmentId.value;
        form.put(route('report.assign.technician.update', { id: assignmentId }), {
            preserveScroll: true,
            onSuccess: () => {
                alert('Assignment updated successfully.');
            },
            onFinish: closeModal,
        });
    }
}

function editAssignment(assignment) {
    editingAssignmentId.value = assignment.id;
    selectedField.value = assignment.p_id || assignment.technician_id || assignment.p_id;
    form.solution = assignment.solution || '';
    form.id = props.reportDetail.id;
    showAssignModal.value = true;
}

function deleteAssignment(assignmentId) {
    if (!confirm('Delete this assignment?')) return;

    // route name may differ; adjust if your backend uses different names
    router.delete(route('report.destroye.assign.technician', {id: assignmentId }), {
        preserveScroll: true,
        onSuccess: () => { alert('Assignment deleted'); }
    });
}

function closeCase() 
{
    if (!confirm('Are your sure you wan to close the case?')) return;
    router.post(route('report.close', { id: props.reportDetail.id }), {
        preserveScroll: true,
        onSuccess: () => { alert('Case closed'); }
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
                                    reportDetail.status === 'CLOSED' ? 'bg-green-100 text-green-800' :
                                    reportDetail.status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                                    reportDetail.status === 'IN PROGRESS' ? 'bg-blue-100 text-blue-800' :
                                    reportDetail.status === 'OVERDUE' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
                                ]"
                            >
                                {{ reportDetail.status }}
                            </span>
                        </div>
                        <div class="text-xs text-gray-400 mt-2">{{ reportDetail.created_date }}</div>
                        <DangerButton v-if="reportDetail.status != 'CLOSED'" @click="closeCase">close case</DangerButton>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-md font-semibold text-gray-800">Solution summary</h4>
                    <div>
                        <SecondaryButton @click="openAssign">Add solution</SecondaryButton>
                    </div>
                </div>

                <div v-if="reportDetail.report_progress && reportDetail.report_progress.length">
                    
                    <ul class="space-y-3">
                        <li v-for="a in reportDetail.report_progress" :key="a.id" class="border rounded p-3 flex items-start justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-800">{{ a.person.name }}</div>
                                <div class="text-xs text-gray-500">Field: {{ a.person.field }}</div>
                                <div class="mt-2 text-sm text-gray-700">{{ a.solution }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ a.created_date }}</div>
                            </div>
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-black-800">{{ a.person.status }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                               
                                <button class="p-2 rounded hover:bg-gray-100 text-yellow-600" @click="editAssignment(a)" title="Edit" aria-label="Edit assignment">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path d="M2 14.5V18h3.5L16.873 6.627l-3.5-3.5L2 14.5zM19.3 5.3a1 1 0 000-1.4l-3.2-3.2a1 1 0 00-1.4 0l-1.7 1.7 4.6 4.6 1.7-1.7z"/>
                                    </svg>
                                </button>
                                <button class="p-2 rounded hover:bg-gray-100 text-red-600" @click="deleteAssignment(a.id)" title="Delete" aria-label="Delete assignment">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 100 2h12a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 011 1v6a1 1 0 11-2 0V9a1 1 0 011-1zm6 1a1 1 0 10-2 0v6a1 1 0 102 0V9z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
                <div v-else class="text-sm text-gray-500">No assignments yet.</div>
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
                        <Selectbox v-model="selectedField" :options="filterOption" placeholder="Select in charge person"></Selectbox>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="solution" value="Solution" />
                        <Textarea id="solution" v-model="form.solution" rows="4" class="mt-1 block w-full" placeholder="Describe the solution taken" />
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-2">
                    <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                    <DangerButton @click="submitAssign">Save</DangerButton>
                </div>
            </div>
        </Modal>

        <!-- Solution Modal -->

    </AuthenticatedLayout>
</template>