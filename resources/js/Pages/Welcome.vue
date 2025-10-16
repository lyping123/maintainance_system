<script setup>
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});
</script>

<template>
    <Head title="Maintenance System" />

    <div class="min-h-screen bg-gradient-to-b from-slate-50 to-slate-100 dark:from-zinc-900 dark:to-zinc-800 flex items-center justify-center">
        <div class="container mx-auto px-6 lg:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-10">
                <div class="w-full lg:w-1/2">
                    <div class="bg-white dark:bg-zinc-900 shadow rounded-lg p-8">
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Maintenance Reporting System</h1>
                        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">Report issues, assign technicians, and track progress all in one place.</p>

                        <div class="mt-6 flex space-x-3">
                            <Link v-if="canLogin" :href="route('login')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Log in</Link>
                            <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">Register</Link>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm text-slate-600 dark:text-slate-400">
                            <div class="p-3 rounded border bg-slate-50 dark:bg-zinc-800">
                                <div class="font-semibold">Submit Report</div>
                                <div class="text-xs mt-1">Students or staff can submit an issue quickly.</div>
                            </div>
                            <div class="p-3 rounded border bg-slate-50 dark:bg-zinc-800">
                                <div class="font-semibold">Assign Technicians</div>
                                <div class="text-xs mt-1">Assign people based on skills and availability.</div>
                            </div>
                            <div class="p-3 rounded border bg-slate-50 dark:bg-zinc-800">
                                <div class="font-semibold">Track Progress</div>
                                <div class="text-xs mt-1">Monitor updates and close reports when resolved.</div>
                            </div>
                            <div class="p-3 rounded border bg-slate-50 dark:bg-zinc-800">
                                <div class="font-semibold">Files & Photos</div>
                                <div class="text-xs mt-1">Attach images or documents to each report.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2">
                    <div class="rounded-lg overflow-hidden shadow-lg bg-gradient-to-br from-blue-600 to-sky-500 text-white p-8">
                        <h2 class="text-xl font-semibold">Latest Pending Reports</h2>
                        <p class="mt-2 text-sm opacity-90">Quick glance of what's waiting for action</p>

                        <ul class="mt-4 space-y-3">
                            <li class="flex items-start justify-between bg-white/10 rounded p-3">
                                <div>
                                    <div class="font-semibold">Room 205 - Light not working</div>
                                    <div class="text-xs opacity-80">Reported 2025-10-12</div>
                                </div>
                                <div class="text-xs bg-white/20 rounded px-2 py-1">PENDING</div>
                            </li>
                            <li class="flex items-start justify-between bg-white/10 rounded p-3">
                                <div>
                                    <div class="font-semibold">AC malfunction - Block A</div>
                                    <div class="text-xs opacity-80">Reported 2025-10-10</div>
                                </div>
                                <div class="text-xs bg-white/20 rounded px-2 py-1">IN PROGRESS</div>
                            </li>
                        </ul>

                        <div class="mt-6 text-sm opacity-90">This is a demo overview. After login you'll see live data.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
                               
