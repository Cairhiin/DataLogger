<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                URL Events
            </h2>
        </template>
        <div>
            <div class="grid grid-cols-5 items-center gap-8 p-2 font-bold rounded-t
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div class="col-span-2">Date</div>
                <div>Data</div>
                <div>Route</div>
                <div>Application ID</div>
            </div>
            <event-list :events="messages" @show-details="showDetails" />
        </div>
        <pagination :links="links" />
    </app-layout>
</template>
<script>
import Pagination from '@/Components/Custom/Pagination.vue';
import EventList from '@/Components/Events/EventList.vue';
import AppLayout from '@/Layouts/AppLayout.vue';


export default {
    Data() {
        return {

        }
    },
    components: {
        EventList,
        AppLayout,
        Pagination
    },
    props: {
        messages: Array,
        links: Array
    },
    methods: {
        showDetails(id) {
            this.selectedId = id;
            this.modalIsShowing = true;
            const log = this.getSelectedLog();
            this.encrypted = {
                original_data: log.original_data,
                new_data: log.new_data
            };
        },
    }
}
</script>
