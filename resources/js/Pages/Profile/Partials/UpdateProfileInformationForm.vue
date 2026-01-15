<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm } from "@inertiajs/vue3";
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

const form = useForm({
    name: props.user?.name ?? "",
    email: props.user?.email ?? "",
    phone: props.user?.phone ?? "",
    address: props.user?.address ?? "",
});
</script>

<template>
    <section>
        <header class="mb-6">
            <h2 class="text-xl font-bold text-gray-100">
                {{ __("Profile Information") }}
            </h2>
            <p class="mt-1 text-sm text-gray-400">
                {{
                    __(
                        "Update your account's profile information and email address"
                    )
                }}.
            </p>
        </header>

        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="space-y-5"
        >
            <div>
                <InputLabel
                    for="name"
                    :value="__('Name')"
                    class="text-gray-300"
                />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-red-500 focus:ring-red-500"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel
                    for="phone"
                    :value="__('Phone')"
                    class="text-gray-300"
                />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-red-500 focus:ring-red-500"
                    v-model="form.phone"
                    autocomplete="phone"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div>
                <InputLabel
                    for="address"
                    :value="__('Address')"
                    class="text-gray-300"
                />
                <TextInput
                    id="address"
                    type="text"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-red-500 focus:ring-red-500"
                    v-model="form.address"
                    autocomplete="address"
                />
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <div>
                <InputLabel
                    for="email"
                    :value="__('Email')"
                    class="text-gray-300"
                />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-gray-900 border-gray-700 text-gray-100 focus:border-red-500 focus:ring-red-500"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user?.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-400">
                    {{ __("Your email address is unverified") }}.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-red-400 hover:text-red-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                        {{
                            __("Click here to re-send the verification email")
                        }}.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-400"
                >
                    {{
                        __(
                            "A new verification link has been sent to your email address"
                        )
                    }}.
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <PrimaryButton
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900"
                >
                    {{ __("Save") }}
                </PrimaryButton>

                <Transition
                    enter-from-class="opacity-0"
                    leave-to-class="opacity-0"
                    class="transition ease-in-out"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-green-400"
                    >
                        {{ __("Saved") }}.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
