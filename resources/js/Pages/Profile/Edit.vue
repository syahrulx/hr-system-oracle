<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import EmployeeTabs from "@/Components/Tabs/EmployeeTabs.vue";
import { __ } from "@/Composables/useTranslations.js";

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    user: {
        type: Object,
    },
});

// Generate avatar initials from user name
const getInitials = (name) => {
    if (!name) return "??";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

// Format member since date
const formatDate = (date) => {
    if (!date) return "N/A";
    return new Date(date).toLocaleDateString(undefined, {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <Head :title="__('Profile')" />

    <AuthenticatedLayout>
        <template #tabs>
            <EmployeeTabs />
        </template>

        <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div
                class="mb-8 bg-gradient-to-br from-[#18181b] to-[#1f1f23] rounded-2xl border border-red-900/20 shadow-xl overflow-hidden relative"
            >
                <!-- Background glow effect -->
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-red-600/20 to-transparent rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"
                ></div>

                <div
                    class="relative z-10 p-8 flex flex-col md:flex-row items-center gap-6"
                >
                    <!-- Avatar with glowing effect -->
                    <div class="relative">
                        <div
                            class="w-24 h-24 rounded-full bg-gradient-to-br from-red-500 to-red-700 flex items-center justify-center text-3xl font-bold text-white shadow-[0_0_30px_rgba(220,38,38,0.4)]"
                        >
                            {{ getInitials(user?.name) }}
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-white mb-1">
                            {{ user?.name }}
                        </h1>

                        <div
                            class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-3"
                        >
                            <!-- Role Badge -->
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                                :class="{
                                    'bg-red-900/50 text-red-200 border border-red-500/30':
                                        ['admin', 'supervisor'].includes(
                                            user?.user_role?.toLowerCase()
                                        ),
                                    'bg-purple-900/50 text-purple-200 border border-purple-500/30':
                                        user?.user_role?.toLowerCase() ===
                                        'owner',
                                    'bg-blue-900/50 text-blue-200 border border-blue-500/30':
                                        ['employee', 'staff'].includes(
                                            user?.user_role?.toLowerCase()
                                        ),
                                }"
                            >
                                {{ user?.user_role || "Staff" }}
                            </span>

                            <!-- Employee ID -->
                            <span
                                v-if="user?.user_id"
                                class="text-gray-400 text-sm"
                            >
                                ID:
                                <span class="font-mono text-gray-300"
                                    >#{{ user?.user_id }}</span
                                >
                            </span>
                        </div>

                        <p class="text-gray-400 text-sm">{{ user?.email }}</p>

                        <p
                            v-if="user?.hired_on"
                            class="text-gray-500 text-xs mt-2"
                        >
                            {{ __("Member since") }}
                            {{ formatDate(user?.hired_on) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- 2-Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Main Profile Form (8 cols) -->
                <div class="lg:col-span-8">
                    <div
                        class="bg-[#18181b] backdrop-blur border border-red-500/20 rounded-2xl p-6 shadow-[0_0_20px_-5px_rgba(220,38,38,0.15)]"
                    >
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            :user="user"
                        />
                    </div>
                </div>

                <!-- Password/Security Form (4 cols) -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Security Tip -->
                    <div
                        class="bg-amber-900/20 border border-amber-500/30 rounded-xl p-4"
                    >
                        <div class="flex items-start gap-3">
                            <svg
                                class="w-5 h-5 text-amber-400 mt-0.5 flex-shrink-0"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            <div>
                                <p
                                    class="text-amber-200 text-sm font-semibold mb-1"
                                >
                                    {{ __("Security Tip") }}
                                </p>
                                <p class="text-amber-300/70 text-xs">
                                    {{
                                        __(
                                            "Update your password regularly and use a strong, unique password for this account."
                                        )
                                    }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Password Form -->
                    <div
                        class="bg-[#18181b] backdrop-blur border border-red-500/20 rounded-2xl p-6 shadow-[0_0_20px_-5px_rgba(220,38,38,0.15)]"
                    >
                        <UpdatePasswordForm />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
