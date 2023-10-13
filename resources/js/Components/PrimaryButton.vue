<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" class="inline-flex items-center px-4 py-2
    bg-gray-800 border border-transparent rounded font-semibold text-xs text-white uppercase tracking-widest
    hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500
    dark:bg-green-700 dark:focus:bg-green-800 dark:active:bg-green-800 dark:hover:bg-green-800 dark:text-zinc-100
    disabled:bg-gray-700 hover:disabled:bg-gray-700
    focus:ring-offset-2 transition ease-in-out duration-150">
        <slot />
    </button>
</template>
