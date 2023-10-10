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
        <div>
            <form @submit.prevent="submit">
                <div>
                    <label for="filter">Filter:</label>
                    <select id="filter" v-model="form.filter">
                        <option disabled value="">Kies een filter optie</option>
                        <option value="model">Data</option>
                        <option value="route">Route</option>
                        <option value="event">Event</option>
                    </select>
                    <input id="param" v-model="form.param" />
                </div>
                <primary-button type="submit">Filter</primary-button>
            </form>
        </div>
        <modal :show="modalIsShowing">
            <event-details :event="modalContent" />
            <div class="border-t border-zinc-300 dark:border-zinc-700 p-4 flex justify-end gap-4">
                <secondary-button @click="hideDetails">Sluiten</secondary-button>
                <secondary-button @click="decryptData">Decrypt</secondary-button>
                <danger-button>Verwijderen</danger-button>
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
        }
    },
    computed: {
        modalContent() {
            const log = this.logs.data.filter(log => log.id === this.selectedId)[0];

            if (this.decrypted.original_data) {
                log.original_data = this.decrypted.original_data;
            }

            if (this.decrypted.new_data) {
                log.new_data = this.decrypted.new_data;
            }

            return log;
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
        AppLayout,
    },
    methods: {
        getSelectedLog() {
            return this.logs.data.filter(log => log.id === this.selectedId)[0];
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
            axios.get(`/event/logs/${this.selectedId}/`)
                .then(response => {
                    this.decrypted.original_data = response.data.original_data;
                    this.decrypted.new_data = response.data.new_data;
                })
                .catch(function (error) {
                    console.log(error);
                })
                .finally(function () {

                });
        },
        submit() {
            router.get('/event/logs', { [this.form.filter]: this.form.param });
        }
    }
}
</script>
