<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed, ref } from 'vue';

function timeAgo(date) {
    if (!date) return '';
    const d = new Date(date);
    const seconds = Math.floor((Date.now() - d.getTime()) / 1000);
    if (seconds < 60) return `${seconds}s ago`;
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    return `${days}d ago`;
}

const props = defineProps({
    reports: { type: Array, default: () => [] },
    history: { type: Array, default: () => [] },
    recent_progress: { type: Array, default: () => [] },
});


const latestPending = computed(() => (props.reports || []).filter(r => String(r.status).toLowerCase() === 'pending').slice(0, 6));
const latestInProgress = computed(() => (props.reports || []).filter(r => String(r.status).toLowerCase() === 'in progress').slice(0, 6));
const recentHistory = computed(() => {
    // prefer explicit history prop, fallback to recent_progress
    // console.log(props.history.report_progress.length);
    const src = (props.history && props.history.length) ? props.history : props.recent_progress || [];
    // return latest 10 sorted by created_at or created_date
    return (src || []).slice().sort((a, b) => new Date(b.created_at || b.created_date || 0) - new Date(a.created_at || a.created_date || 0)).slice(0, 10);
});

function viewReport(id) {
    if (!id) return;
    router.get(route('report.detail.show', { id }));
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
            
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Dashboard</h1>
                        <div class="text-sm text-gray-500">Overview of system activity</div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600"><button >Pending: <span   class="inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">{{ (props.reports || []).filter(r => String(r.status).toLowerCase() === 'pending').length }}</span></button></div>
                        <div class="text-sm text-gray-600" ><button>In progress: <span  class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">{{ (props.reports || []).filter(r => String(r.status).toLowerCase() === 'in progress').length }}</span></button></div>
                        <div class="text-sm text-gray-600">Solved: <span class="inline-block bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">{{ (props.history || []).filter(r => String(r.status).toLowerCase() === 'closed').length }}</span></div>
                        <div class="text-sm text-gray-600">Total Reports: <span class="font-medium">{{ (props.reports || []).length }}</span></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Pending Reports -->
                    <div class="col-span-2 bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-3">Latest Pending Reports</h3>
                        <div v-if="latestPending.length">
                            <ul class="divide-y">
                                <li v-for="r in latestPending" :key="r.id" class="py-3 flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center space-x-2">
                                            <div class="font-medium text-gray-800">{{ r.report_type }} — {{ r.places }}</div>
                                            
                                            <div :class="[
                                                'text-xs text-white rounded px-2 py-0.5',
                                                r.emergency === 'Medium' ? 'bg-yellow-500' :
                                                r.emergency === 'High' ? 'bg-red-500' :
                                                r.emergency === 'Low' ? 'bg-blue-500' : ''
                                            ]" >
                                                
                                                {{ r.priority || r.emergency || '' }}</div>
                                        </div>
                                        <div class="text-sm text-gray-500">{{ r.s_name }} • <span title="{{ r.created_date || r.created_at }}">{{ timeAgo(r.created_date || r.created_at) }}</span></div>
                                        <div class="text-sm text-gray-700 mt-1" :title="r.report_issue">{{ (r.report_issue || '').length > 120 ? (r.report_issue || '').slice(0,120) + '...' : r.report_issue }}</div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="viewReport(r.id)" class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">View</button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="text-sm text-gray-500">No pending reports.</div>
                    </div>

                    <!-- History Log -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold mb-3">History Log</h3>
                        <div v-if="recentHistory.length">
                            <ul class="space-y-3">
                                <li v-for="h in recentHistory" :key="h.id" class="text-sm text-gray-700 border rounded p-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div>
                                                <div class="flex items-center space-x-3">
                                                    <div class="font-medium text-gray-800">{{ h.report_type || 'Event' }}</div>
                                                    <span v-if="h.status=='CLOSED'" class="text-xs font-semibold px-2 py-0.5 rounded bg-green-100 text-green-800">Solved</span>
                                                    <span v-else class="text-xs font-semibold px-2 py-0.5 rounded bg-blue-100 text-blue-800">Update</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">{{ h.s_name || h.user_name || '' }} • {{ timeAgo(h.created_date || h.created_at) }}</div>
                                            </div>
                                            <div class="text-xs text-gray-500">Solve in: {{ h.created_date || h.created_at }}</div>
                                            <div class="text-xs text-black-500">Solve By:{{ h.technician_name }}</div>
                                            <div class="mt-2 text-sm text-gray-600">{{ h.note || h.solution || h.report_issue || '' }}</div>
                                            
                                        </div>
                                        <div>
                                            <button v-if="h.report_id || h.reportId || h.r_id" @click="viewReport(h.report_id || h.reportId || h.r_id)" class="text-xs px-2 py-1 bg-gray-100 rounded">Open</button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div v-else class="text-sm text-gray-500">No history yet.</div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
