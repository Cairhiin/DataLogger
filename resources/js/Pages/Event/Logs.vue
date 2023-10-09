<template>
    <AppLayout title="Log Entries">
        <div>
            <div class="grid grid-cols-5 items-center gap-8 p-2 font-bold
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div class="col-span-2">Datum</div>
                <div>Data</div>
                <div>Route</div>
                <div>Event</div>
            </div>
            <EventList :events="logs.data" @show-details="showDetails" />
        </div>
        <pagination :links="logs.links" />
        <Modal :show="modalIsShowing">
            <div class="p-4">{{ modalContent }}</div>
            <div class="border-t border-gray-300 p-4 flex justify-end gap-4">
                <SecondaryButton>Sluiten</SecondaryButton>
                <DangerButton>Verwijderen</DangerButton>
            </div>
        </Modal>
    </AppLayout>
</template>

<script>
import Pagination from '@/Components/Custom/Pagination.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EventList from '@/Components/Events/EventList.vue';
import Modal from '@/Components/Modal.vue';
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
        AppLayout,
    },
    methods: {
        showDetails(id) {
            this.selectedId = id;
            this.modalIsShowing = true;
        }
    }
}
</script>
