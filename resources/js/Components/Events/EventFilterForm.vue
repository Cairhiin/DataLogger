<template>
    <section class="py-8">
        <form class="dark:bg-zinc-800/25 bg-zinc-200 p-4 rounded" @submit.prevent="submit">
            <label class="dark:text-zinc-100 uppercase font-bold text-sm" for="filter">Choose a filter</label>
            <div class="flex gap-4 mt-2 mb-4 ">
                <select id="filter" v-model="form.filter" title="filter"
                    class="rounded dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100 p-2">
                    <option disabled value="">Choose a filter option</option>
                    <option value="model">Data</option>
                    <option value="route">Route</option>
                    <option value="app">Application ID</option>
                </select>
                <select id="param" v-model="form.param" :disabled="!filterType.length" title="param" class="rounded
                    dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100 p-2">
                    <option disabled value="">Choose a filter value</option>
                    <option v-for="route in filterType" :value="route">{{ route }}</option>
                </select>
            </div>
            <div class="flex gap-8">
                <primary-button :disabled="!form.filter || !form.param">Filter</primary-button>
                <secondary-button @click="$emit('onReset')" type="button">Reset</secondary-button>
            </div>
        </form>
    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import axios from 'axios';
export default {
    data() {
        return {
            form: {
                filter: null,
                param: null,
            },
            filterType: [],
            routes: [],
            apps: [],
            models: []
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
    components: {
        PrimaryButton,
        SecondaryButton
    },
    computed: {
        filters: function () {
            return {
                "route": () => this.routes.map(r => r.route),
                "model": () => this.models.map(m => m.model),
                "app": () => this.apps.map(a => a.app_id)
            };
        },
        getFilterType() {
            return this.form.filter;
        }

    },
    watch: {
        getFilterType: function (filterType) {
            this.filterType = this.filters[filterType]();
        }
    },
    methods: {
        submit() {
            if (this.form.filter && this.form.param) {
                this.$emit('onSubmit', this.form.filter, this.form.param);
            }
        }
    },
    props: {
        events: Array
    }
}
</script>
