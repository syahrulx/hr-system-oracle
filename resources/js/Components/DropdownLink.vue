<script setup>
import { Link, useForm } from "@inertiajs/vue3";

const props = defineProps({
    href: {
        type: String,
        default: "#",
    },
    method: {
        type: String,
        default: "get",
    },
    as: {
        type: String,
        default: "a",
    },
});

const form = useForm({});

const handleClick = (e) => {
    if (props.method.toLowerCase() === "post") {
        e.preventDefault();
        form.post(props.href);
    }
};
</script>

<template>
    <Link
        v-if="method.toLowerCase() !== 'post'"
        :href="href"
        :method="method"
        :as="as"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 transition duration-150 ease-in-out"
    >
        <slot />
    </Link>
    <button
        v-else
        type="button"
        @click="handleClick"
        class="block w-full px-4 py-2 text-left text-sm leading-5 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 transition duration-150 ease-in-out"
    >
        <slot />
    </button>
</template>
