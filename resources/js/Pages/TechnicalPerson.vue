<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router,useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Selectbox from '@/Components/Selectbox.vue';

import DangerButton from '@/Components/DangerButton.vue';
import { ref, computed } from 'vue';


const editingPerson = ref(false);

const form=useForm({
    id:null,
    name: '',
    email: '',
    phone: '',
    field: '',
    address: '',
});


const peopleModal=ref(false);

function openPeoplesModal() 
{
    editingPerson.value=false;
    peopleModal.value=true;
}

const props = defineProps({
    technicalPeople: Array,
    filters: Object,
});

// search and filter state (preserve from server-provided filters if present)
const search = ref(props.filters?.search || '');
const selectedField = ref(props.filters?.field || '');

const fieldsOptions = computed(() => {
    const set = new Set();
    (props.technicalPeople || []).forEach(p => { if (p.field) set.add(p.field); });
    const arr = Array.from(set).sort();
    return arr.map(f => ({ value: f, label: f }));
});

function searchPeople() {
    router.get(route('technical.index'), { search: search.value, field: selectedField.value }, { preserveState: true, replace: true });
}

function clearFilters() {
    search.value = '';
    selectedField.value = '';
    router.get(route('technical.index'), {}, { preserveState: true, replace: true });
}


// function viewPerson(id) {
// 	router.get(route('technical.show', { id }));
// }
function closeModal() 
{
    peopleModal.value=false;
    form.clearErrors();
    form.reset();
}
function submitPerson(){
    form.post(route('technical.store'), {
        onSuccess: () => {
            closeModal();
            alert('Technical person added successfully.');
        },
        onError: () => {
            alert('Technical person could not be added.');
        },
        onFinish: () => form.reset(),
    });
}

function editPerson(person)
{
    editingPerson.value=true;
    form.name=person.name;
    form.email=person.email;
    form.phone=person.phone;
    form.field=person.field;
    form.address=person.address;
    form.id=person.id;
    peopleModal.value=true;
}

function updatePerson(id)
{
    router.put(route('technical.update', { id }), form.data(), {
        preserveState: true,
        onSuccess: () => {
            closeModal();
            alert('Technical person updated successfully.');  
        },
        onError: () => {
            alert('Technical person could not be updated.');
        },
        onFinish: () => form.reset(),
    });
}

function deletePerson(id) 
{
    if (!confirm('Delete this technical person?')) return;
    form.delete(route('technical.destroy', { id }), { preserveState: true, onSuccess: () => { alert('Deleted'); } });
}

</script>

<template>
	<Head title="Technical People" />

	<AuthenticatedLayout>
		<template #header>
			<div class="flex items-center justify-between">
				<h2 class="text-xl font-semibold leading-tight text-gray-800">Technical People</h2>
				<div>
					<SecondaryButton @click="openPeoplesModal">Add Person</SecondaryButton>
				</div>
			</div>
		</template>
        
		
        <div class="mt-4 flex items-center space-x-3">
            <div class="w-1/2">
                <input v-model="search" @keyup.enter="searchPeople" type="text" placeholder="Search name or email..." class="w-full rounded-md border-gray-300 p-2" />
            </div>
            <div class="w-1/4">
                <Selectbox v-model="selectedField" :options="fieldsOptions" placeholder="Filter by field" />
            </div>
            <div class="flex items-center space-x-2">
                <button @click="searchPeople" class="px-3 py-1 bg-blue-600 text-white rounded">Search</button>
                <button @click="clearFilters" class="px-3 py-1 bg-gray-200 rounded">Clear</button>
            </div>
        </div>

		<div class="bg-white rounded-lg shadow overflow-hidden mt-4">
			<table class="min-w-full divide-y divide-gray-200">
				<thead class="bg-gray-50">
					<tr>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
						<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
						<th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
					</tr>
				</thead>
				<tbody class="bg-white divide-y divide-gray-200">
					<tr v-for="person in technicalPeople" :key="person.id">
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ person.name }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ person.email }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ person.phone }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ person.field }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ person.address }}</td>
						<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
							<button @click="editPerson(person)" class="text-yellow-600 hover:text-yellow-900 mr-2">Edit</button>
							<button @click="deletePerson(person.id)" class="text-red-600 hover:text-red-900">Delete</button>
						</td>
					</tr>
					<tr v-if="technicalPeople.length === 0">
						<td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No technical people found.</td>
					</tr>
				</tbody>
			</table>
		</div>
        <Modal :show="peopleModal" @close="closeModal">
            <div class="p-6 bg-white rounded-lg shadow-lg w-full max-w-lg mx-auto">
                    <!-- Header -->
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2 flex items-center justify-between">
                        <span>🛠️ Add Technical Person</span>
                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                    </h2>

                    <!-- Form -->
                    <div class="space-y-4">
                        <!-- Report Type -->
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                ref="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g. ALEX TAN"
                            />
                        </div>
                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                ref="email"
                                v-model="form.email"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g. alex@gamil.com"
                            />
                        </div>
                        <div>
                            <InputLabel for="phone" value="Contact Number" />
                            <TextInput
                                id="phone"
                                ref="phone"
                                v-model="form.phone"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g. +60123456789"
                            />
                        </div>
                        <div>
                            <InputLabel for="field" value="Field" />
                            <TextInput
                                id="field"
                                ref="field"
                                v-model="form.field"
                                type="text"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g. electronics"
                            />
                        </div>
                        <div>
                            <InputLabel for="address" value="Address" />
                            <textarea
                                id="address"
                                ref="address"
                                v-model="form.address"
                                required
                                rows="4"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-y"
                                placeholder="e.g. area city state"
                            ></textarea>
                        </div>

                        
                    <!-- Footer -->
                    <div class="mt-6 flex justify-end space-x-3 border-t pt-3">
                        <SecondaryButton @click="closeModal" class="px-4 py-2 text-sm">
                            Cancel
                        </SecondaryButton>
                        <DangerButton v-if="editingPerson===false"
                            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                            :class="{ 'opacity-50': form.processin   }"
                            :disabled="form.processing"
                            @click="submitPerson"
                        >
                            Submit
                        </DangerButton>
                        <DangerButton v-else
                            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-md"
                            :class="{ 'opacity-50': form.processin   }"
                            :disabled="form.processing"
                            @click="updatePerson(form.id)"
                        >
                            Update
                        </DangerButton>
                    </div>
                </div>
            </div>
        </Modal>

	</AuthenticatedLayout>
</template>
