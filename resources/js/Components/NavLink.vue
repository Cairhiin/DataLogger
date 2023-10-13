<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-2 py-3 text-base font-heading leading-5 font-bold text-zinc-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out dark:text-zinc-300'
        : 'inline-flex items-center px-2 py-3 text-base font-heading leading-5 text-zinc-700 hover:text-sky-700 hover:border-sky-500 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out dark:text-zinc-100 dark:hover:text-zinc-400 dark:hover:border-sky-400';
});
</script>

<template>
    <Link :href="href" :class="classes">
    <slot />
    </Link>
</template>
