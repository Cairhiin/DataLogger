<template>
    <app-layout title="Database Events">
        <template #header>
            <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                Database Events
            </h2>
        </template>
        <div>
            <div class="grid grid-cols-4 items-center gap-8 p-2 font-bold rounded-t
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div>Date</div>
                <div>IP</div>
                <div>Route</div>
                <div>Application ID</div>
            </div>
            <event-list :events="logsList.data" @show-details="showDetails" />
        </div>
        <pagination :links="logsList.links" />
        <div class="mt-6 dark:bg-zinc-800/25 bg-zinc-100 rounded">
            <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400">Enter time frame</h3>
            <div class="p-4">
                <vue-date-picker v-model="date" range @closed="getLogsInRange" />
            </div>
        </div>
        <event-filter-form :events="logsList.data" @onSubmit="onSubmit" @onReset="onReset" :routes="routes" :apps="apps"
            :models="models" />
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" :isDecrypted="isDecrypted" />
            <event-modal-content :error="error" :isLoading="isLoading" @decrypt-data="decryptData"
                @hide-details="hideDetails" @delete-log="deleteLog" :hasDecrypt="true" :hasDelete="true" />
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
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';

export default {
    data() {
        return {
            date: null,
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
            logsList: this.logs,
            routes: [],
            apps: [],
            models: []
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
        VueDatePicker
    },
    computed: {
        isDecrypted() {
            return this.decrypted.original_data !== null || this.decrypted.new_data !== null;
        },
        getLogsInRange() {
            if (this.date) {
                this.isLoading = true;
                const from = `${this.date[0].getFullYear()}-${this.date[0].getMonth() + 1}-${this.date[0].getDate()}`;
                const to = `${this.date[1].getFullYear()}-${this.date[1].getMonth() + 1}-${this.date[1].getDate()}`;

                axios.get(`/event/logs/from/${from}/to/${to}`)
                    .then(res => {
                        if (!res.error) {
                            this.logsList = res.data;
                        }
                    })
                    .catch(err => console.error(err))
                    .finally(this.isLoading = false);
            }
        },
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
                new_data: log.new_data,
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

            this.error = '';
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
    },
    mounted() {
        axios.get(`/event/logs/routes/`)
            .then(response => {
                if (response.data.error) {
                    this.error = response.data.error.status;
                } else {
                    this.routes = response.data;
                }
            });
        axios.get(`/event/logs/apps/`)
            .then(response => {
                if (response.data.error) {
                    this.error = response.data.error.status;
                } else {
                    this.apps = response.data;
                }
            });
        axios.get(`/event/logs/models/`)
            .then(response => {
                if (response.data.error) {
                    this.error = response.data.error.status;
                } else {
                    this.models = response.data;
                }
            });
    },
}
</script>
