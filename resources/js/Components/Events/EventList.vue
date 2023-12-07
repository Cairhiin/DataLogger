<template>
    <div v-if="events.length === 0" class="p-2 bg-sky-500/25">No events found!</div>
    <div v-for="(log, index) in events" :key="log.id" @click="$emit('showDetails', log.id)" class="grid grid-cols-4 items-center gap-8 p-2 border-b
                odd:bg-sky-500/25 even:bg-sky-400/25 border-sky-200 hover:bg-sky-300/75
                dark:odd:bg-slate-900/75 dark:even:bg-slate-900/25
                dark:border-slate-800/75 dark:text-zinc-100 dark:hover:bg-stone-900 cursor-pointer"
        :class="{ 'border-b-0 rounded-b': index === events.length - 1 }">
        <div>
            {{ formatDate(log.created_at) }} </div>
        <div>
            {{ log.ip_address }}
        </div>
        <div>
            {{ log.route }}
        </div>
        <div>
            {{ log.app_id }}
        </div>
    </div>
</template>
<script>
import { formatDate } from '@/Utils/index.js';
export default {
    props: {
        events: Array
    },
    methods: {
        formatDate
    },
    emits: {
        showDetails: null,
    }
}
</script>
