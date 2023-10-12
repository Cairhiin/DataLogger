<template>
    <app-layout title="Log Entries">
        <div>
            <div class="grid grid-cols-5 items-center gap-8 p-2 font-bold
            bg-zinc-900 text-zinc-100 dark:bg-zinc-100 dark:text-zinc-900">
                <div class="col-span-2">Date</div>
                <div>Data</div>
                <div>Route</div>
                <div>Application ID</div>
            </div>
            <event-list :events="logsList.data" @show-details="showDetails" />
        </div>
        <pagination :links="logsList.links" />
        <div>
            <form @submit.prevent="submit">
                <div>
                    <label for="filter">Filter:</label>
                    <select id="filter" v-model="form.filter">
                        <option disabled value="">Choose a filter option</option>
                        <option value="model">Data</option>
                        <option value="route">Route</option>
                        <option value="event">Application ID</option>
                    </select>
                    <input id="param" v-model="form.param" />
                </div>
                <primary-button type="submit">Filter</primary-button>
            </form>
        </div>
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <div class="border-t border-zinc-300 dark:border-zinc-700 p-4 flex justify-between items-center">
                <div class="text-red-600 font-bold text-sm uppercase"><span
                        :class="{ 'fa fa-solid fa-warning': error }"></span>&nbsp;&nbsp;{{
                            error }}
                </div>
                <div class="flex justify-end  gap-6">
                    <secondary-button @click="hideDetails">
                        <span class="text-base fa fa-solid fa-xmark"></span>
                        &nbsp;&nbsp;Close</secondary-button>
                    <secondary-button @click="decryptData">
                        <span :class="{ 'text-sm fa fa-solid fa-spinner fa-spin': isLoading }"></span>
                        <span :class="{ 'text-sm fa fa-solid fa-key': !isLoading }"></span>
                        &nbsp;&nbsp;Decrypt</secondary-button>
                    <danger-button @click="deleteLog">Delete</danger-button>
                </div>
            </div>
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
            form: {
                filter: null,
                param: null,
            },
            isLoading: false,
            error: null,
            logsList: this.logs
        }
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
        AppLayout,
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
                });
        },
        submit() {
            router.get('/event/logs', { [this.form.filter]: this.form.param });
        }
    }
}
</script>
