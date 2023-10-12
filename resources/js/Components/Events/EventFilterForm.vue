<template>
    <section class="py-8">
        <form @submit.prevent="submit" class="dark:bg-zinc-800/25 p-4 rounded">
            <label class="dark:text-zinc-100 uppercase font-bold text-sm" for="filter">Choose a filter</label>
            <div class="flex gap-4 mt-2 mb-4 ">
                <select id="filter" v-model="form.filter"
                    class="rounded dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100 p-2">
                    <option disabled value="">Choose a filter option</option>
                    <option value="model">Data</option>
                    <option value="route">Route</option>
                    <option value="app">Application ID</option>
                </select>
                <select id="param" v-model="form.param"
                    class="rounded dark:bg-zinc-800 dark:border-zinc-700 dark:text-zinc-100 p-2">
                    <option disabled value="">Choose a filter value</option>
                    <option v-for="route in filterType" :value="route">{{ route }}</option>
                </select>
            </div>
            <div class="flex gap-8">
                <primary-button type="submit">Filter</primary-button>
                <secondary-button>Reset</secondary-button>
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
        axios.get(`/api/event/logs/routes/`,
            { headers: { Authorization: `Bearer ${import.meta.env.VITE_API_KEY}` } })
            .then(response => {
                if (response.data.error) {
                    this.error = response.data.error.status;
                } else {
                    this.routes = response.data;
                }
            });
        axios.get(`/api/event/logs/apps/`,
            { headers: { Authorization: `Bearer ${import.meta.env.VITE_API_KEY}` } })
            .then(response => {
                if (response.data.error) {
                    this.error = response.data.error.status;
                } else {
                    this.apps = response.data;
                }
            });
        axios.get(`/api/event/logs/models/`,
            { headers: { Authorization: `Bearer ${import.meta.env.VITE_API_KEY}` } })
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
            this.$emit('submit')
        }
    },
    props: {
        events: Array
    }
}
</script>
