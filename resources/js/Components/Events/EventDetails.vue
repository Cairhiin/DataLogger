<template>
    <div class="p-4">
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">ID</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.id }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Datum:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ formatDate(event.created_at) }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">IP Adres:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.ip_address }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Data:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.model }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Route:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.route }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Type:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.event_type }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Nieuwe Data:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ formatData(event.new_data) }}</div>
        </div>
        <div class="flex items-center">
            <div class="w-32 text-sm font-bold dark:text-zinc-400 text-zinc-700 uppercase">Oude Data:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ formatData(event.original_data) }}</div>
        </div>
    </div>
</template>
<script>
import { formatDate } from '@/Utils/index.js';
export default {
    props: {
        event: Object
    },
    methods: {
        formatDate,
        formatData(data) {
            const d = Object.keys(data);
            return d.reduce((sum, key, index, d) => sum + `${key}: ${data[key]}${index < d.length - 1 ? ', ' : ''}`, '');
        }
    },

}
</script>
