<template>
    <app-layout title="URL events">
        <template #header>
            <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                URL Events
            </h2>
        </template>
        <log-file-list :files="fileList" @delete-file="deleteFile" @backup-file="backupFile" />
        <div>
            <div class="grid grid-cols-5 items-center gap-8 p-2 font-bold rounded-t
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div class="col-span-2">Date</div>
                <div>User</div>
                <div>Route</div>
                <div>Application ID</div>
            </div>
            <event-list :events="messages" @show-details="showDetails" />
        </div>
        <pagination :links="links" />
        <event-filter-form :events="messages" @onSubmit="onSubmit" @onReset="onReset" :routes="uniqueValues.route"
            :apps="uniqueValues.app_id" :models="uniqueValues.model" />
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <event-modal-content :error="error" :isLoading="isLoading" @hide-details="hideDetails"
                @decrypt-data="decryptData" :hasDecrypt="true" :hasDelete="false" />
        </modal>
    </app-layout>
</template>

<script>
import { router } from '@inertiajs/vue3'
import Pagination from '@/Components/Custom/Pagination.vue';
import EventList from '@/Components/Events/EventList.vue';
import EventDetails from '@/Components/Events/EventDetails.vue';
import EventModalContent from '@/Components/Events/EventModalContent.vue';
import EventFilterForm from '@/Components/Events/EventFilterForm.vue';
import Modal from '@/Components/Modal.vue';
import LogFileList from '@/Components/Events/LogFileList.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

export default {
    data() {
        return {
            fileList: [],
            modalIsShowing: false,
            modalContent: {},
            error: '',
            isLoading: false,
            selectedId: ''
        }

    },
    mounted() {
        this.fileList = this.files;
    },
    components: {
        EventList,
        EventDetails,
        EventModalContent,
        EventFilterForm,
        Pagination,
        Modal,
        LogFileList,
        AppLayout
    },
    props: {
        messages: Array,
        links: Array,
        files: Array,
        uniqueValues: Object,
        url: String
    },
    methods: {
        showDetails(id) {
            this.selectedId = id;
            this.modalIsShowing = true;
            this.modalContent = this.messages.find(message => message.id === id);
        },
        hideDetails() {
            this.modalIsShowing = false;
        },
        backupFile(fileName) {
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
        },
        decryptData() {
            this.isLoading = true;
            console.log(this.selectedId)
            axios.get(`/event/messages/${this.selectedId}/`)
                .then(response => {
                    if (response.data.error) {
                        this.error = response.data.error.status;
                    } else {
                        this.decrypted.original_data = response.data.original_data;
                        this.decrypted.new_data = response.data.new_data;
                    }
                })
                .catch(error => {
                    this.error = error;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        deleteFile(fileName) {
            if (!confirm("Are you certain you want to delete this log? It cannot be undone!")) {
                return false;
            }

            axios.delete(`/event/files/${fileName}/`)
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
        },
        onSubmit(filter, param) {
            router.get(`/event/files?file=${this.url}`, { [filter]: param });
        },
        onReset() {
            router.get(`/event/files?file=${this.url}`);
        }
    }
}
</script>
