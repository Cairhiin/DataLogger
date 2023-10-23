<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                Dashboard
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                    <div class="bg-zinc-100 dark:bg-zinc-800 rounded relative mb-8 pb-2">
                        <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400 mb-2">Latest Database Events</h3>
                        <ul>
                            <li v-for="log in logs" class="grid grid-cols-3 px-2">
                                <div>{{ log.event_type }}</div>
                                <div>{{ log.route }}</div>
                                <div>{{ log.model }}</div>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-zinc-100 dark:bg-zinc-800 rounded relative mb-8 pb-2">
                        <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400 mb-2">Latest URL Events</h3>
                        text
                    </div>
                    <div v-if="$page.props.auth.user.role_id === 3"
                        class="lg:col-span-2 bg-zinc-100 dark:bg-zinc-800 rounded relative mb-8">
                        <h3 class=" text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4
                    py-2 rounded-t text-zinc-600 dark:text-zinc-400 mb-2">Latest Members (if ADMIN)</h3>
                        <ul>
                            <li v-for="member in users" class="grid grid-cols-3 px-2 pb-2">
                                <div>{{ member.name }}</div>
                                <div>{{ member.role.name }}</div>
                                <div>{{ formatDate(member.created_at) }}</div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
import { formatDate } from '@/Utils/index.js';

export default {
    data() {
        return {

        }
    },
    components: {
        AppLayout
    },
    props: {
        users: Array,
        files: Array,
        logs: Array,
        fileMessages: Array
    },
    methods: {
        formatDate
    },
}
</script>
