<template>
    <div class="p-4">
        <div class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">ID</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.id }}</div>
        </div>
        <div class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">Date:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ formatDate(event.created_at) }}</div>
        </div>
        <div class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">Account:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.user.name }}</div>
        </div>
        <div class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">IP Address:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.ip_address }}</div>
        </div>
        <div v-if="event.model" class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">Model:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.model }}</div>
        </div>
        <div class="flex items-start text-sm">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">Route:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.route }}</div>
        </div>
        <div v-if="event.event_type" class="flex items-start text-sm ">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">Type:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.event_type }}</div>
        </div>
        <div v-if="event.new_data" class="grid grid-cols-5 items-start text-sm ">
            <div class="font-bold dark:text-zinc-400 text-zinc-500 uppercase">Updated Data:</div>
            <div class="col-span-4 text-zinc-900 dark:text-zinc-100" :class="{ 'truncate': !isDecrypted }">{{
                event.new_data }}</div>
        </div>
        <div v-if="event.original_data" class="grid grid-cols-5 items-start text-sm ">
            <div class="font-bold dark:text-zinc-400 text-zinc-500 uppercase">Old Data:</div>
            <div class="col-span-4 text-zinc-900 dark:text-zinc-100" :class="{ 'truncate': !isDecrypted }">{{
                event.original_data }}</div>
        </div>
        <div v-if="event.remote_user_id" class="flex items-start text-sm ">
            <div class="w-32 font-bold dark:text-zinc-400 text-zinc-500 uppercase">User ID:</div>
            <div class="text-zinc-900 dark:text-zinc-100">{{ event.remote_user_id }}</div>
        </div>
    </div>
</template>
<script>
import { formatDate } from '@/Utils/index.js';
export default {
    props: {
        event: Object,
        isDecrypted: { type: Boolean, default: false }
    },
    methods: {
        formatDate,
        /* formatData(data) {
            const d = Object.keys(data);
            return d.reduce((sum, key, index, d) => sum + `${key}: ${data[key]}${index < d.length - 1 ? ', ' : ''}`, '');
        } */
    },

}
</script>
