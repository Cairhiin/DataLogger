<template>
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
</template>
<script>
export default {
    data() {
        return {
            selectedId: 0,
            modalIsShowing: false,
            form: {
                filter: null,
                param: null,
            },
        }
    },
}</script>
