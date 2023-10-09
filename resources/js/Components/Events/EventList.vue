<template>
    <div v-for="log in events" :key="log.id" @click="$emit('showDetails', log.id)" class="grid grid-cols-5 items-center gap-8 p-2 border-b
                odd:bg-sky-300 even:bg-sky-200 dark:odd:bg-slate-900/75 dark:even:bg-slate-900/25
                dark:border-slate-800 dark:text-zinc-100 dark:hover:bg-stone-900 cursor-pointer">
        <div>
            {{ formatDate(log.created_at) }} </div>
        <div>
            {{ log.model }}
        </div>
        <div>
            {{ log.route }}
        </div>
        <div>
            {{ log.event_type }}
        </div>
    </div>
</template>
<script>
export default {
    props: {
        events: Array
    },
    methods: {
        formatDate(d) {
            const time = new Date(new Date(d).getTime());
            const date = new Date(d).toLocaleDateString('nl-NL', { day: 'numeric', month: 'numeric', year: 'numeric' });

            const hours = time.getHours() < 10 ? `0${time.getHours()}` : time.getHours();
            const minutes = time.getMinutes() < 10 ? `0${time.getMinutes()}` : time.getMinutes();
            const seconds = time.getSeconds() < 10 ? `0${time.getSeconds()}` : time.getSeconds();
            return `${date} (${hours}:${minutes}:${seconds})`;
        }
    },
    emits: {
        showDetails: null,
    }
}
</script>
