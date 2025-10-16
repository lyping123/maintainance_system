<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head,router,useForm   } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Selectbox from '@/Components/Selectbox.vue';
import TextInput from '@/Components/TextInput.vue';
import FileInput from '@/Components/FileInput.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

import { ref,computed } from 'vue';

const form=useForm({
    id:null,
    report_type:'',
    report_issue:'',
    places:'',
    emergency:'',
    attachment:null,
});

const props = defineProps({
    reports: Object,
    filters: Object,
    hostels: Object,
});



const confiAddReport= ref(false);
const confieditReport=ref(false);

const selectedField = ref(props.filters?.field || '');

const filterOption = computed(() => {
    const map = new Map();
    (props.hostels || []).forEach(p => {
        if (p && (p.id !== undefined) && p.h_address) {
           map.set(p.id,p.h_address);
        }
    });
    const arr = Array.from(map, ([id, name]) => ({ label: name, value: name }));
    return arr.sort((a, b) => String(a.label).localeCompare(String(b.label)));
});


// reactive search input
// const search = ref(props.filters.search || '');
function showAddReportModal()
{
    confiAddReport.value = true;
}

const closeModal = () => {
    confiAddReport.value = false;
    confieditReport.value = false;
    form.clearErrors();
    form.reset();
};

function submit_report()
{
    form.places = selectedField.value;
    form.post(route('report.store'), {
        preserveScroll: true,
        onSuccess:closeModal,
        onError:()=>{console.log('error')},
        onFinish:form.reset,
    });
}


function searchReports() {
    router.get(route('reports.index'), { search: search.value }, { preserveState: true, replace: true });
}


function viewReport(id) {
    
    router.get(route('report.detail.show', { id: id }));
}

function editReport(report) {
    confieditReport.value = true;
    form.id=report.id;
    form.report_type=report.report_type;
    form.report_issue=report.report_issue;
    form.places=report.places;
    form.emergency=report.emergency;
    
}

function submit_edit_report()
{
    let id=form.id;
    // console.log(form.attachment);
    form.post(route('report.update', { id: id }), {
        method:'put',
        preserveScroll: true,
        onSuccess:closeModal,
        onError:()=>{console.log('error')},
        onFinish:form.reset,
    });
}


function deleteReport(id){
    router.delete(route('report.destroy', { id: id }), { preserveScroll: true, replace: true,onSuccess:()=>{alert('Report deleted successfully')} });

}

</script>

<template>
    <Head title="reports list"  />
    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Reports List
            </h2>
            <div class="flex items-center">
                    <input
                        v-model="search"
                        @keyup.enter="searchReports"
                        type="text"
                        placeholder="Search reports..."
                        class="border rounded-lg px-3 py-1 text-sm"
                    />
                    <button
                        @click="searchReports"
                        class="ml-2 bg-blue-600 text-white text-xs px-3 py-1 rounded hover:bg-blue-700"
                    >
                        Search
                    </button>
                </div>
            
        </template>
            <Modal :show="confiAddReport" @close="closeModal">
                <div class="p-6 bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto">
                    <!-- Header -->
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 flex items-center justify-between">
                        <span>📝 Submit New Report</span>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                    </h2>

                    <!-- Form -->
                    <div class="space-y-4">
                        <!-- Report Type -->
                        <div>
                            <InputLabel for="report_type" value="Report Type" />
                            <TextInput
                                id="report_type"
                                ref="report_type"
                                v-model="form.report_type"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Electrical Issue, Maintenance"
                            />
                        </div>

                        <!-- Report Issue -->
                        <div>
                            <InputLabel for="report_issue" value="Report Issue / Description" />
                            <textarea
                                id="report_issue"
                                ref="report_issue"
                                v-model="form.report_issue"
                                required
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2"
                                placeholder="Describe the issue briefly..."
                            ></textarea>
                        </div>

                        <!-- Location -->
                        <div>
                            <InputLabel for="places" value="Location" />
                            <!-- <TextInput
                                id="places"
                                ref="places"
                                v-model="form.places"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Block A, Room 205"
                            /> -->
                            <Selectbox v-model="selectedField" :options="filterOption"> </Selectbox>
                            
                        </div>
                        <div>
                            <InputLabel for="attachment" value="Attachment">Attachment</InputLabel>
                            <FileInput id="attachment" ref="attachment" name="attachment" v-model="form.attachment" multiple="multiple"  />
                        </div>

                        <!-- Emergency Level -->
                        <div>
                            <InputLabel for="emergency" value="Emergency Level" />
                            <select
                                id="emergency"
                                ref="emergency"
                                v-model="form.emergency"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                                <option value="" disabled>Select level</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-3 border-t pt-3">
                        <SecondaryButton @click="closeModal" class="px-4 py-2 text-sm">
                            Cancel
                        </SecondaryButton>
                        <DangerButton
                            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            @click="submit_report"
                        >
                            Submit Report
                        </DangerButton>
                    </div>
                </div>
            </Modal>


            <Modal :show="confieditReport" @close="closeModal">
                <div class="p-6 bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto">
                    <!-- Header -->
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 flex items-center justify-between">
                        <span>📝 Modify report</span>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                    </h2>

                    <!-- Form -->
                    <div class="space-y-4">
                        <!-- Report Type -->
                        <div>
                            <InputLabel for="report_type" value="Report Type" />
                            <TextInput
                                id="report_type"
                                ref="report_type"
                                v-model="form.report_type"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Electrical Issue, Maintenance"
                            />
                        </div>

                        <!-- Report Issue -->
                        <div>
                            <InputLabel for="report_issue" value="Report Issue / Description" />
                            <textarea
                                id="report_issue"
                                ref="report_issue"
                                v-model="form.report_issue"
                                required
                                rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm p-2"
                                placeholder="Describe the issue briefly..."
                            ></textarea>
                        </div>

                        <!-- Location -->
                        <div>
                            <InputLabel for="places" value="Location" />
                            <TextInput
                                id="places"
                                ref="places"
                                v-model="form.places"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Block A, Room 205"
                            />
                        </div>
                        <div>
                            <InputLabel for="attachment" value="Attachment">Attachment</InputLabel>
                            <FileInput id="attachment" ref="attachment" name="attachment" v-model="form.attachment" multiple="multiple"  />
                        </div>
                        

                        <!-- Emergency Level -->
                        <div>
                            <InputLabel for="emergency" value="Emergency Level" />
                            <select
                                id="emergency"
                                ref="emergency"
                                v-model="form.emergency"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                                <option value="" disabled>Select level</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-3 border-t pt-3">
                        <SecondaryButton @click="closeModal" class="px-4 py-2 text-sm">
                            Cancel
                        </SecondaryButton>
                        <DangerButton
                            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                            :class="{ 'opacity-50': form.processing }"
                            :disabled="form.processing"
                            v-model="form.id"
                            @click="submit_edit_report"
                        >
                            Modify report
                        </DangerButton>
                    </div>
                </div>
            </Modal>

            <div class="flex flex-col bg-gray-100 rounded-lg shadow-lg overflow-hidden p-2">
                
                <div class="report-list mt-6">
                    <div class="mb-4 flex justify-end">
                        <button
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700 transition"
                            @click="showAddReportModal"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Report
                        </button>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 shadow rounded-lg overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Places </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Report type</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Report issue</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Issue date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="(report, index) in reports" :key="report.id">
                                <td class="px-4 py-2">{{ index + 1 }}</td>
                                <td class="px-4 py-2">{{ report.s_name }}</td>
                                <td class="px-4 py-2">{{ report.places }}</td>
                                <td class="px-4 py-2">{{ report.report_type }}</td>
                                <td class="px-4 py-2">{{ report.report_issue }}</td>
                                <td class="px-4 py-2">
                                    <span
                                        :class="[
                                            'px-2 py-1 rounded text-xs font-semibold',
                                            report.status === 'CLOSED' ? 'bg-green-100 text-green-800' :
                                            report.status === 'IN PROGRESS' ? 'bg-yellow-100 text-yellow-800' :
                                            report.status === 'PENDING' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ report.status }}
                                    </span>
                                </td>
                                <td>{{ report.created_date }}</td>
                                <td class="px-4 py-2 space-x-2">
                                    <button
                                        @click="viewReport(report.id)"
                                        class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition"
                                    >
                                        View
                                    </button>
                                    <button
                                        @click="editReport(report)"
                                        class="inline-flex items-center px-3 py-1 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition"
                                    >
                                        Edit
                                    </button>
                                    <button class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition"
                                    @click="deleteReport(report.id)"
                                    >Delete</button>
                                </td>
                            </tr>
                            <tr v-if="reports.length === 0">
                                <td colspan="8" class="px-4 py-4 text-center text-gray-500">No reports found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
            </div>
            

    </AuthenticatedLayout>
    
</template>

<!-- <script>
export default {
    name: 'ReportsList',
    data() {
        return {
            reports: [
                
                // Add more sample reports as needed
            ]
        };
    },
    methods: {
        statusClass(status) {
            return {
                'text-success': status === 'Completed',
                'text-warning': status === 'Pending',
                'text-danger': status === 'Overdue'
            };
        },
        viewReport(id) {
            // Implement view logic
            alert(`View report ${id}`);
        },
        editReport(id) {
            // Implement edit logic
            alert(`Edit report ${id}`);
        }
    }
};
</script> -->

