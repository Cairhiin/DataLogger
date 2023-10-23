<template>
    <section class="py-8">
        <form class="dark:bg-zinc-800/25 bg-zinc-100 rounded" @submit.prevent="submit">
            <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400">Filter the data</h3>
            <div class="p-4">
                <label class="dark:text-zinc-100 uppercase font-bold text-sm" for="filter">Choose a filter</label>
                <div class="flex gap-4 mb-4">
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
            </div>
        </form>
    </section>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

export default {
    data() {
        return {
            form: {
                filter: null,
                param: null,
            },
            filterType: [],
        }
    },
    components: {
        PrimaryButton,
        SecondaryButton
    },
    computed: {
        filters: function () {
            return {
                "route": () => this.routes.map(r => r.route ? r.route : r),
                "model": () => this.models.map(m => m.model ? m.model : m),
                "app": () => this.apps.map(a => a.app_id ? a.app_id : a)
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
        events: Array,
        routes: Array,
        apps: Array,
        models: Array
    }
}
</script>
