<template>
    <app-layout title="Log Entries">
        <template #header>
            <h2>
                Database Events
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
            <event-list :events="logsList.data" @show-details="showDetails" />
        </div>
        <pagination :links="logsList.links" />
        <event-filter-form :events="logsList.data" @onSubmit="onSubmit" @onReset="onReset" />
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <event-modal-content :error="error" :isLoading="isLoading" @decrypt-data="decryptData"
                @hide-details="hideDetails" @delete-log="deleteLog" />
        </modal>
    </app-layout>
</template>

<script>
import { router } from '@inertiajs/vue3'
import axios from 'axios';
import Pagination from '@/Components/Custom/Pagination.vue';
import DangerButton from '@/Components/DangerButton.vue';
import EventList from '@/Components/Events/EventList.vue';
import Modal from '@/Components/Modal.vue';
import EventDetails from '@/Components/Events/EventDetails.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import EventModalContent from '@/Components/Events/EventModalContent.vue';
import EventFilterForm from '@/Components/Events/EventFilterForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
    data() {
        return {
            decrypted: {
                original_data: null,
                new_data: null
            },
            encrypted: {
                original_data: null,
                new_data: null
            },
            selectedId: 0,
            modalIsShowing: false,
            isLoading: false,
            error: null,
            logsList: this.logs
        }
    },
    props: {
        logs: Object
    },
    components: {
        Pagination,
        EventList,
        Modal,
        PrimaryButton,
        SecondaryButton,
        DangerButton,
        EventDetails,
        EventModalContent,
        EventFilterForm,
        AppLayout,
    },
    computed: {
        modalContent() {
            const log = this.getSelectedLog();

            if (this.decrypted.original_data) {
                log.original_data = this.decrypted.original_data;
            }

            if (this.decrypted.new_data) {
                log.new_data = this.decrypted.new_data;
            }

            return log;
        },
    },
    methods: {
        getSelectedLog() {
            return this.logsList.data.filter(log => log.id === this.selectedId)[0];
        },
        showDetails(id) {
            this.selectedId = id;
            this.modalIsShowing = true;
            const log = this.getSelectedLog();
            this.encrypted = {
                original_data: log.original_data,
                new_data: log.new_data
            };
        },
        hideDetails() {
            this.modalIsShowing = false;
            this.error = null;
            const log = this.getSelectedLog();

            if (this.decrypted.original_data) {
                log.original_data = this.encrypted.original_data;
                this.decrypted.original_data = null;
            }

            if (this.decrypted.new_data) {
                log.new_data = this.encrypted.new_data;
                this.decrypted.new_data = null;
            }
        },
        decryptData() {
            this.isLoading = true;
            axios.get(`/event/logs/${this.selectedId}/`)
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
        deleteLog(id) {
            if (!confirm("Are you certain you want to delete this log event? It cannot be undone!")) {
                return false;
            }

            axios.delete(`/event/logs/${this.selectedId}/`)
                .then(response => {
                    if (response.data.error) {
                        this.error = response.data.error.status;
                    } else {
                        this.modalIsShowing = false;
                        this.logsList.data = this.logsList.data.filter(log =>
                            log.id !== this.selectedId
                        );
                    }
                })
                .catch(error => {
                    this.error = error;
                }).finally(() => {
                    if (this.decrypted.original_data) {
                        log.original_data = this.encrypted.original_data;
                        this.decrypted.original_data = null;
                    }

                    if (this.decrypted.new_data) {
                        log.new_data = this.encrypted.new_data;
                        this.decrypted.new_data = null;
                    }
                });
        },
        onSubmit(filter, param) {
            router.get('/event/logs', { [filter]: param });
        },
        onReset() {
            router.get('/event/logs');
        }
    }
}
</script>
