<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, usePage } from "@inertiajs/vue3";
import EmployeeTabs from "@/Components/Tabs/EmployeeTabs.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import GenericModal from "@/Components/GenericModal.vue";
import { useToast } from "vue-toastification";
import VueDatePicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import Swal from "sweetalert2";
import { Switch } from "@headlessui/vue";
import ToolTip from "@/Components/ToolTip.vue";
import Card from "@/Components/Card.vue";
import { inject, computed } from "vue";
import { __ } from "@/Composables/useTranslations.js";
import dayjs from "dayjs";

const props = defineProps({
    employee: Object,
    href: String,
    shifts: Object,
    roles: Object,
});

// Map Oracle roles to frontend values
const mapRoleToFrontend = (oracleRole) => {
    const role = (oracleRole || "").toLowerCase();
    if (role === "supervisor") return "admin";
    if (role === "staff") return "employee";
    return role; // Return as-is if already admin/employee
};

const form = useForm({
    name: props.employee.name,
    ic_number: props.employee.national_id,
    email: props.employee.email,
    phone: props.employee.phone,
    address: props.employee.address,
    hired_on: props.employee.hired_on,
    role: mapRoleToFrontend(props.employee.user_role),
});

// Check if current user is owner (owners can only edit permissions)
const isOwner = computed(() => {
    const role = (usePage().props.auth?.user?.role || '').toLowerCase();
    return role === 'owner';
});

const shiftForm = useForm({
    name: "",
    start_time: "",
    end_time: "",
    shift_payment_multiplier: "",
    description: "",
});

const submit = () => {
    form.hired_on = dayjs(form.hired_on).format("YYYY-MM-DD");
    form.put(route("employees.update", { id: props.employee.id }), {
        preserveScroll: true,
        onError: () => {
            useToast().error(__("Error Editing Employee"));
        },
        onSuccess: () => {
            useToast().success(__("Employee Edited Successfully"));
        },
    });
};
const destroy = () => {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton:
                "mx-4 text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900",
            cancelButton:
                "text-white bg-gray-700 hover:bg-gray-600 focus:outline-none focus:ring-4 focus:ring-gray-500 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2",
        },
        buttonsStyling: false,
    });
    swalWithBootstrapButtons
        .fire({
            title: __("Are you sure?"),
            text: __("You won't be able to revert this!"),
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: __("Yes, Delete!"),
            cancelButtonText: __("No, Cancel!"),
            reverseButtons: true,
        })
        .then((result) => {
            if (result.isConfirmed) {
                form.delete(
                    route("employees.destroy", { id: props.employee.id }),
                    {
                        preserveScroll: true,
                        onError: () => {
                            useToast().error(__("Error Removing Employee"));
                        },
                        onSuccess: () => {
                            Swal.fire(__("Employee Removed!"), "", "success");
                        },
                    }
                );
            }
        });
};

const submitShift = () => {
    shiftForm.post(route("shifts.store"), {
        preserveScroll: true,
        onError: () => {
            useToast().error(__("Error Creating Shift"));
        },
        onSuccess: () => {
            useToast().success(__("Shift Created Successfully"));
            document.getElementById("closeShiftModal").click();
            shiftForm.reset();
            form.shift_id = props.shifts.length;
        },
    });
};
</script>

<template>
    <Head :title="__('Employee Edit')" />
    <AuthenticatedLayout>
        <template #tabs>
            <EmployeeTabs />
        </template>
        <div class="py-8 min-h-screen bg-[#0f172a]/20">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 animate-fade-in-up">
                <div class="glass-panel p-8 relative overflow-hidden group">
                    <!-- Subtle Glow -->
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-red-500/5 rounded-full blur-3xl group-hover:bg-red-500/10 transition-all duration-700"></div>
                    
                    <div class="relative flex items-center gap-4 mb-10 border-b border-white/5 pb-6">
                        <div class="p-3 rounded-xl bg-red-600/10 border border-red-500/20">
                            <ModifyIcon class="w-6 h-6 text-red-400"/>
                        </div>
                        <div>
                            <h1 class="card-header-modern">{{ __("Edit Profile") }}</h1>
                            <p class="text-xs text-slate-500 font-medium tracking-wide">{{ employee.name }} • ID: {{ employee.id }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="relative space-y-8">
                        <!-- Identity Section -->
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                {{ __('Identity Information') }}
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <InputLabel for="name" :value="__('Full Name')" class="text-slate-400 font-bold text-[10px] uppercase ml-1"/>
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full !bg-slate-900/50 !border-white/10 !rounded-xl focus:!border-red-500/50 transition-all"
                                        :class="{
                                            'border-2 border-red-500': form.errors.name,
                                            'opacity-60 cursor-not-allowed': isOwner,
                                        }"
                                        v-model="form.name"
                                        required
                                        autofocus
                                        autocomplete="name"
                                        :disabled="isOwner"
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                                
                                <div class="space-y-1.5">
                                    <InputLabel for="ic_number" :value="__('National ID')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                    <TextInput
                                        id="ic_number"
                                        type="number"
                                        class="mt-1 block w-full !bg-slate-900/50 !border-white/10 !rounded-xl focus:!border-red-500/50 transition-all text-data tracking-widest"
                                        :class="{
                                            'border border-red-500': form.errors.ic_number,
                                            'opacity-60 cursor-not-allowed': isOwner,
                                        }"
                                        v-model="form.ic_number"
                                        required
                                        pattern="[0-9]{14}"
                                        autocomplete="off"
                                        :disabled="isOwner"
                                    />
                                    <InputError class="mt-2" :message="form.errors.ic_number"/>
                                </div>
                            </div>
                        </div>

                        <!-- Contact & Location -->
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Connectivity & Reach') }}
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <InputLabel for="phone" :value="__('Phone Number')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                    <TextInput
                                        id="phone"
                                        type="text"
                                        class="mt-1 block w-full !bg-slate-900/50 !border-white/10 !rounded-xl focus:!border-red-500/50 transition-all"
                                        :class="{
                                            'border border-red-500': form.errors.phone,
                                            'opacity-60 cursor-not-allowed': isOwner,
                                        }"
                                        v-model="form.phone"
                                        required
                                        autocomplete="off"
                                        :disabled="isOwner"
                                    />
                                    <InputError class="mt-2" :message="form.errors.phone" />
                                </div>
                                <div class="space-y-1.5">
                                    <InputLabel for="email" :value="__('Email Address')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                    <TextInput
                                        id="email"
                                        type="email"
                                        class="mt-1 block w-full !bg-slate-900/50 !border-white/10 !rounded-xl focus:!border-red-500/50 transition-all"
                                        :class="{
                                            'border border-red-500': form.errors.email,
                                            'opacity-60 cursor-not-allowed': isOwner,
                                        }"
                                        v-model="form.email"
                                        required
                                        autocomplete="off"
                                        :disabled="isOwner"
                                    />
                                    <InputError class="mt-2" :message="form.errors.email" />
                                </div>
                            </div>
                            
                            <div class="space-y-1.5">
                                <InputLabel for="address" :value="__('Residential Address')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                <TextInput
                                    id="address"
                                    type="text"
                                    class="mt-1 block w-full !bg-slate-900/50 !border-white/10 !rounded-xl focus:!border-red-500/50 transition-all"
                                    :class="{
                                        'border border-red-500': form.errors.address,
                                        'opacity-60 cursor-not-allowed': isOwner,
                                    }"
                                    v-model="form.address"
                                    required
                                    autocomplete="off"
                                    :disabled="isOwner"
                                />
                                <InputError class="mt-2" :message="form.errors.address" />
                            </div>
                        </div>

                        <!-- Professional Status -->
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                {{ __('Employment Details') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-1.5">
                                    <InputLabel for="hired_on" :value="__('Hire Date')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                    <VueDatePicker
                                        id="hired_on"
                                        v-model="form.hired_on"
                                        class="block w-full"
                                        :class="{
                                            'border border-red-500': form.errors.hired_on,
                                            'opacity-60 cursor-not-allowed': isOwner,
                                        }"
                                        :enable-time-picker="false"
                                        :dark="inject('isDark').value"
                                        required
                                        :disabled="isOwner"
                                    ></VueDatePicker>
                                    <InputError class="mt-2" :message="form.errors.hired_on" />
                                </div>
                                <div class="space-y-1.5">
                                    <InputLabel for="role" :value="__('Permissions Level')" class="text-slate-400 font-bold text-[10px] uppercase ml-1" />
                                    <select
                                        id="role"
                                        class="fancy-selector !bg-slate-900/50 !border-white/10 !rounded-xl focus:!ring-red-500/50 focus:!border-red-500/50 transition-all"
                                        v-model="form.role"
                                    >
                                        <option value="admin">Admin / Supervisor</option>
                                        <option value="employee">Employee / Staff</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.role" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Bar -->
                        <div class="flex items-center justify-between mt-12 pt-10 border-t border-white/5">
                            <button type="button" 
                                    @click="destroy"
                                    class="px-6 py-2.5 rounded-xl text-sm font-black text-rose-500 hover:bg-rose-500/10 border border-rose-500/20 transition-all flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                {{ __("Delete Employee") }}
                            </button>
                            
                            <div class="flex gap-4">
                                <PrimaryButton
                                    class="!bg-slate-800/80 hover:!bg-slate-700 !text-white !rounded-xl px-8 !border-white/5 shadow-xl transition-all"
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                >
                                    {{ __("Update Record") }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
