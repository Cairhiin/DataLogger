<template>
    <app-layout title="Log Entries">
        <div>
            <div class="grid grid-cols-5 items-center gap-8 p-2 font-bold
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div class="col-span-2">Datum</div>
                <div>Data</div>
                <div>Route</div>
                <div>Event</div>
            </div>
            <event-list :events="logs.data" @show-details="showDetails" />
        </div>
        <pagination :links="logs.links" />
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <div class="border-t border-zinc-300 dark:border-zinc-700 p-4 flex justify-end gap-4">
                <secondary-button @click="hideDetails">Sluiten</secondary-button>
                <danger-button>Verwijderen</danger-button>
            </div>
        </modal>
    </app-layout>
</template>

<script>
import Pagination from '@/Components/Custom/Pagination.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EventList from '@/Components/Events/EventList.vue';
import Modal from '@/Components/Modal.vue';
import EventDetails from '@/Components/Events/EventDetails.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    data() {
        return {
            selectedId: 0,
            modalIsShowing: false
        }
    },
    computed: {
        modalContent() {
            return this.logs.data.filter(log => log.id === this.selectedId)[0];
        }
    },
    props: {
        logs: Object
    },
    components: {
        Pagination,
        EventList,
        Modal,
        SecondaryButton,
        DangerButton,
        EventDetails,
        AppLayout,
    },
    methods: {
        showDetails(id) {
            this.selectedId = id;
            this.modalIsShowing = true;
        },
        hideDetails() {
            this.modalIsShowing = false;
        }
    }
}
</script>
