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
        <log-file-list :files="fileList" @delete-file="deleteFile" @backup-file="backUp" />
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <event-modal-content :error="error" :isLoading="isLoading" @decrypt-data="decryptData"
                @hide-details="hideDetails" @delete-log="deleteLog" />
        </modal>
    </app-layout>
</template>

<script>
import Pagination from '@/Components/Custom/Pagination.vue';
import EventList from '@/Components/Events/EventList.vue';
import EventDetails from '@/Components/Events/EventDetails.vue';
import EventModalContent from '@/Components/Events/EventModalContent.vue';
import Modal from '@/Components/Modal.vue';
import LogFileList from '@/Components/Events/LogFileList.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

export default {
    data() {
        return { fileList: [] }

    },
    mounted() {
        this.fileList = this.files;
    },
    components: {
        EventList,
        EventDetails,
        EventModalContent,
        Pagination,
        Modal,
        LogFileList,
        AppLayout
    },
    props: {
        messages: Array,
        links: Array,
        files: Array
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
        backUp(fileName) {
            axios.get(`/event/files/${fileName}/copy`)
                .then(response => {
                    if (response.data.status === 'Error') {
                        this.error = response.data.error.status;
                    } else {
                        this.fileList = response.data;
                    }
                })
                .catch(error => {
                    this.error = error;
                });
        }
    }
}
</script>
