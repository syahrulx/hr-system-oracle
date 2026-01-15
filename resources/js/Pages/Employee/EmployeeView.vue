<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {computed, onMounted} from "vue";
import EmployeeTabs from "@/Components/Tabs/EmployeeTabs.vue";
import FlexButton from "@/Components/FlexButton.vue";
import {useExtractPersonalDetails} from "@/Composables/useExtractPersonalDetails.js";
import HistoryDescriptionList from "@/Components/DescriptionList/HistoryDescriptionList.vue";
import {initModals} from "flowbite";
import {useAgeCalculator} from "@/Composables/useAgeCalculator.js";
import Card from "@/Components/Card.vue";
import ModifyIcon from "@/Components/Icons/ModifyIcon.vue";
import DescriptionList from "@/Components/DescriptionList/DescriptionList.vue";
import DT from "@/Components/DescriptionList/DT.vue";
import DD from "@/Components/DescriptionList/DD.vue";
import DescriptionListItem from "@/Components/DescriptionList/DescriptionListItem.vue";
import GenericModal from "@/Components/GenericModal.vue";
import Table from "@/Components/Table/Table.vue";
import TableBody from "@/Components/Table/TableBody.vue";
import TableHead from "@/Components/Table/TableHead.vue";
import TableRow from "@/Components/Table/TableRow.vue";
import ToolTip from "@/Components/ToolTip.vue";
import {__} from "@/Composables/useTranslations.js";
import dayjs from "dayjs";


let {extractPersonalDetails} = useExtractPersonalDetails()

onMounted(() => {
    initModals();
});

const props = defineProps({
    employee: Object,
})

// Removed - manages relationship deleted
</script>

<template>
    <Head :title="__('Employee View')"/>
    <AuthenticatedLayout>
        <template #tabs>
            <EmployeeTabs/>
        </template>
        <div class="py-8 min-h-screen bg-[#0f172a]/20">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8 animate-fade-in-up">
                <!-- Hero Section / Basic Info -->
                <div class="glass-panel p-8 relative overflow-hidden group">
                    <!-- Background Glow -->
                    <div class="absolute -top-24 -right-24 w-64 h-64 bg-red-500/10 rounded-full blur-3xl group-hover:bg-red-500/20 transition-all duration-700"></div>
                    
                    <div class="relative flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center shadow-lg shadow-red-900/40 border border-red-500/30">
                                <span class="text-3xl font-black text-white uppercase">{{ employee.name.charAt(0) }}</span>
                            </div>
                            <div>
                                <h1 class="card-header-modern mb-1">{{ employee.name }}</h1>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider status-emerald">
                                        {{ employee.user_role ? employee.user_role : __('Employee') }}
                                    </span>
                                    <span class="text-xs text-slate-500 font-medium">ID: {{ employee.id }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-4">
                            <FlexButton v-if="$page.props.auth.user.role === 'admin'"
                                        class="glass-panel !rounded-xl px-6 py-2.5 hover:scale-105 active:scale-95 transition-all text-sm font-bold text-white border-white/10 hover:border-red-500/50"
                                        :href="route('employees.edit', {id: employee.id})">
                                <ModifyIcon class="w-4 h-4 mr-2"/> {{ __('Modify Profile') }}
                            </FlexButton>
                            <FlexButton v-else 
                                        class="glass-panel !rounded-xl px-6 py-2.5 hover:scale-105 active:scale-95 transition-all text-sm font-bold text-white border-white/10"
                                        :href="route('profile.edit', {id: employee.id})">
                                <ModifyIcon class="w-4 h-4 mr-2"/> {{ __('Update Data') }}
                            </FlexButton>
                        </div>
                    </div>

                    <!-- Modern Data Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Personal Details -->
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Identity & Contact') }}</h3>
                            
                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('National ID') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-blue-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.33 0 4 1 4 2v1H5v-1c0-1 2.67-2 4-2z"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-white tracking-widest text-data">{{ employee.national_id }}</span>
                                </div>
                            </div>

                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Phone Number') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-emerald-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <a :href="'tel:' + employee.phone" class="text-sm font-bold text-white hover:text-emerald-400 transition-colors">{{ employee.phone }}</a>
                                </div>
                            </div>

                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Email Address') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-amber-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    </div>
                                    <a :href="'mailto:' + employee.email" class="text-sm font-bold text-white hover:text-amber-400 transition-colors">{{ employee.email }}</a>
                                </div>
                            </div>
                        </div>

                        <!-- Professional Info -->
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Professional Status') }}</h3>
                            
                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Hire Date') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-red-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </div>
                                    <span class="text-sm font-black text-white text-data">{{ dayjs(employee.hired_on).format('DD MMM YYYY') }}</span>
                                </div>
                            </div>

                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Birthday & Age') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-purple-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-1.5-.454M3 20h18M3 15h18M5 15V9a7 7 0 0114 0v6M5 9a2 2 0 012-2h10a2 2 0 012 2M10 7V5a2 2 0 014 0v2"/></svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-white">{{ extractPersonalDetails(employee.national_id).date_localized }}</span>
                                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-wider">{{ useAgeCalculator(extractPersonalDetails(employee.national_id).date) }} {{ __('Years Old') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Gender') }}</label>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-slate-800/50 border border-white/5 group-hover/item:border-pink-500/30 transition-colors">
                                        <svg class="w-4 h-4 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-white">{{ extractPersonalDetails(employee.national_id).isMale ? __('Male') : __('Female') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="lg:col-span-1 space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">{{ __('Location Details') }}</h3>
                            <div class="group/item">
                                <label class="text-[10px] font-bold text-slate-400 uppercase block mb-1.5">{{ __('Residential Address') }}</label>
                                <a :href="'https://www.google.com/maps/search/?api=1&query=' + props.employee.address" 
                                   target="_blank" 
                                   class="block p-4 rounded-xl bg-slate-800/30 border border-white/5 group-hover/item:border-blue-500/30 group-hover/item:bg-slate-800/50 transition-all">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-blue-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="text-xs leading-relaxed text-slate-300 font-medium">{{ employee.address }}</span>
                                    </div>
                                    <div class="mt-3 text-[9px] font-black text-blue-400 uppercase tracking-widest text-right">{{ __('View on Maps') }} →</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Section -->
                <div class="glass-panel p-8">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 rounded-lg bg-red-500/10 border border-red-500/20">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h2 class="text-lg font-black text-white tracking-tight">{{ __('Employee History') }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="glass-panel !rounded-xl p-6 border-white/5 hover:border-white/10 transition-colors">
                            <h4 class="text-xs font-black text-slate-500 uppercase tracking-widest mb-4">{{ __('Shift History') }}</h4>
                            <GenericModal modalId='Shifts Modal'
                                          :title="__('View Full Shift History')" :modalHeader="__('Shift Assignment Records')"
                                          :hasCancel="false" 
                                          titleClass="w-full justify-between glass-panel !bg-slate-800/30 !border-white/5 px-6 py-4 hover:!bg-slate-800/50 transition-all font-bold text-sm text-slate-300">
                                
                                <Table :totalNumber="1" :enablePaginator="false">
                                    <template #Head>
                                        <TableHead class="text-data">{{__('Shift Designation')}}</TableHead>
                                        <TableHead class="text-data">{{__('Active From')}}</TableHead>
                                        <TableHead class="text-data text-right">{{__('Terminated At')}}</TableHead>
                                    </template>

                                    <template #Body>
                                        <TableRow v-for="shift in employee.employee_shifts" :key="shift.id" class="hover:bg-red-500/5 transition-colors">
                                            <TableBody>
                                                <div class="flex items-center gap-3">
                                                    <div class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></div>
                                                    <span class="font-bold text-white">{{shift.shift?.name ?? __('DELETED')}}</span>
                                                </div>
                                            </TableBody>
                                            <TableBody class="text-data text-xs">{{shift.start_date}}</TableBody>
                                            <TableBody class="text-right">
                                                <span v-if="!shift.end_date" class="px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 text-[9px] font-black uppercase tracking-tighter border border-emerald-500/20 shadow-sm">
                                                    {{ __('Current') }}
                                                </span>
                                                <span v-else class="text-data text-xs text-slate-500">
                                                    {{shift.end_date}}
                                                </span>
                                            </TableBody>
                                        </TableRow>
                                    </template>
                                </Table>
                            </GenericModal>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
