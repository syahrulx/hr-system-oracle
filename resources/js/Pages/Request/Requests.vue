<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import FlexButton from "@/Components/FlexButton.vue";
import ReqTabs from "@/Components/Tabs/ReqTabs.vue";
import Card from "@/Components/Card.vue";
import PlusIcon from "@/Components/Icons/PlusIcon.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import TableRow from "@/Components/Table/TableRow.vue";
import TableBodyHeader from "@/Components/Table/TableBodyHeader.vue";
import Table from "@/Components/Table/Table.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import {__} from "@/Composables/useTranslations.js";
import {request_types} from "@/Composables/useRequestTypes.js";
import {request_status_types} from "@/Composables/useRequestStatusTypes.js";
import dayjs from "dayjs";
import {computed} from "vue";

const props = defineProps({
    requests: Object,
    leaveBalances: Array,
    leaveTotals: Object,
})

const balancesObj = computed(() => {
    return props.leaveBalances.reduce((acc, curr) => {
        acc[curr.leave_type] = curr.balance;
        return acc;
    }, {});
});

</script>
<template>
    <Head :title="__('Requests')"/>
    <AuthenticatedLayout>
        <template #tabs>
            <ReqTabs />
        </template>
        <div class="py-10 min-h-screen bg-[#0f172a]/20">
            <div class="max-w-7xl mx-auto px-4 lg:px-8 flex flex-col lg:flex-row gap-8 animate-fade-in-up">
                <!-- Sidebar: Leave Balances -->
                <aside class="w-full lg:w-80 flex-shrink-0 space-y-6">
                    <div class="glass-panel p-6 border-red-500/10 shadow-red-900/5">
                        <div v-if="['admin','owner'].includes($page.props.auth.user.role)">
                            <div class="flex items-center gap-3 mb-6 border-b border-white/5 pb-4">
                                <div class="p-2 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h2 class="text-xs font-black text-white uppercase tracking-widest">{{ __('Approved Leaves') }}</h2>
                            </div>
                            <div class="space-y-3">
                                <div v-for="type in ['Annual Leave', 'Emergency Leave', 'Sick Leave']" :key="type" 
                                     class="flex items-center justify-between p-3 rounded-xl bg-slate-800/30 border border-white/5 group hover:border-white/10 transition-all">
                                    <span class="text-xs font-bold text-slate-300 group-hover:text-white transition-colors flex items-center gap-2">
                                        <div :class="{
                                            'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]': type === 'Annual Leave',
                                            'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]': type === 'Emergency Leave',
                                            'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]': type === 'Sick Leave'
                                        }" class="w-1.5 h-1.5 rounded-full"></div>
                                        {{ type }}
                                    </span>
                                    <span class="text-sm font-black text-white text-data">{{ leaveTotals && leaveTotals[type] ? leaveTotals[type] : 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <div class="flex items-center gap-3 mb-6 border-b border-white/5 pb-4">
                                <div class="p-2 rounded-lg bg-blue-500/10 border border-blue-500/20">
                                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <h2 class="text-xs font-black text-white uppercase tracking-widest">{{ __('My Balances') }}</h2>
                            </div>
                            <div class="space-y-3">
                                <div v-for="type in ['Annual Leave', 'Emergency Leave', 'Sick Leave']" :key="type" 
                                     class="flex items-center justify-between p-3 rounded-xl bg-slate-800/30 border border-white/5 group hover:border-white/10 transition-all">
                                    <span class="text-xs font-bold text-slate-400 group-hover:text-slate-200 transition-colors">{{ type }}</span>
                                    <span class="px-3 py-1 rounded-lg bg-red-900/20 text-red-400 border border-red-500/20 text-xs font-black text-data">
                                        {{ balancesObj[type] || 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security/Tip Panel -->
                    <div class="glass-panel p-6 bg-gradient-to-br from-indigo-500/10 to-transparent border-indigo-500/10">
                        <div class="flex items-start gap-4">
                            <div class="p-2 rounded-lg bg-indigo-500/20 mt-1">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-white uppercase tracking-wider mb-2">{{ __('Request Tip') }}</h4>
                                <p class="text-[10px] text-slate-400 font-medium leading-relaxed">{{ __('Remember to attach relevant documents for emergency leaves to expedite the approval process.') }}</p>
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 space-y-6">
                    <div class="glass-panel p-8 relative overflow-hidden group">
                        <div class="absolute -top-24 -right-24 w-64 h-64 bg-red-500/5 rounded-full blur-3xl group-hover:bg-red-500/10 transition-all duration-700"></div>
                        
                        <div class="relative flex flex-col sm:flex-row sm:justify-between sm:items-center gap-6 mb-10 pb-6 border-b border-white/5">
                            <div>
                                <h1 class="card-header-modern mb-2">{{__('Active Requests')}}</h1>
                                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest">{{ requests.data.length }} {{ __('Entries Found') }}</p>
                            </div>
                            <FlexButton v-if="!['admin','owner'].includes($page.props.auth.user.role)" 
                                        :href="route('requests.create')"
                                        class="glass-panel !bg-red-600/90 hover:!bg-red-500 !text-white !rounded-xl px-6 py-2.5 !border-red-500/50 shadow-xl shadow-red-900/20 transition-all font-black text-xs uppercase tracking-widest flex items-center gap-2">
                                <PlusIcon class="w-4 h-4"/> {{__('New Request')}}
                            </FlexButton>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-white/5">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-white/5 border-b border-white/5">
                                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{__('ID')}}</th>
                                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{__('Employee')}}</th>
                                        <th class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{__('Type')}}</th>
                                        <th class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{__('Duration')}}</th>
                                        <th class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">{{__('Status')}}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-white/5">
                                    <tr v-for="request in requests.data" :key="request.id" 
                                        @click="() => $inertia.visit(route('requests.show', {id: request.id}))"
                                        class="group hover:bg-white/[0.03] transition-colors cursor-pointer">
                                        <td class="px-6 py-5">
                                            <span class="text-xs font-black text-red-500 text-data tracking-widest">#{{request.id}}</span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center border border-white/5 group-hover:border-red-500/30 transition-colors">
                                                    <span class="text-xs font-black text-white uppercase">{{ request.employee_name.charAt(0) }}</span>
                                                </div>
                                                <span class="text-sm font-bold text-white group-hover:text-red-400 transition-colors">{{request.employee_name}}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-xs font-medium text-slate-300">{{ request.type }}</span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-black text-white text-data">{{ dayjs(request.start_date).format('DD MMM') }}</span>
                                                <svg class="w-3 h-3 text-slate-600 my-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                <span class="text-xs font-black text-white text-data">{{ request.end_date ? dayjs(request.end_date).format('DD MMM') : __('N/A') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <span v-if="request.status === 'Pending'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider status-amber">
                                                {{ __('Pending') }}
                                            </span>
                                            <span v-else-if="request.status === 'Approved'" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider status-emerald">
                                                {{ __('Approved') }}
                                            </span>
                                            <span v-else class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider status-rose">
                                                {{ __('Rejected') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
