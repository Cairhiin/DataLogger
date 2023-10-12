<template>
    <div>
        <form @submit.prevent="submit">
            <div>
                <label for="filter">Filter:</label>
                <select id="filter" v-model="form.filter">
                    <option disabled value="">Choose a filter option</option>
                    <option value="model">Data</option>
                    <option value="route">Route</option>
                    <option value="app">Application ID</option>
                </select>
                <select id="param" v-model="form.param">
                    <option disabled value="">Choose a filter value</option>
                    <option v-for="route in filterType" :value="route">{{ route }}</option>
                </select>
            </div>
            <primary-button type="submit">Filter</primary-button>
        </form>
    </div>
</template>

<script>
import PrimaryButton from '@/Components/PrimaryButton.vue';
export default {
    data() {
        return {
            form: {
                filter: null,
                param: null,
            },
            filterType: []
        }
    },
    components: {
        PrimaryButton
    },
    computed: {
        filters: function () {
            return {
                "route": () => new Set(this.events.map(event => event.route)),
                "model": () => new Set(this.events.map(event => event.model)),
                "app": () => new Set(this.events.map(event => event.app_id))
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
