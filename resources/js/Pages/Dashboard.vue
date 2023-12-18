<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-zinc-800 dark:text-zinc-100 leading-tight">
                Dashboard
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-sm">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <section class="bg-zinc-100 dark:bg-zinc-800 rounded relative">
                        <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400">Latest Database Events</h3>
                        <ul>
                            <li v-for="log in logs" class="grid grid-cols-4 items-center px-2 py-2 border-t border-zinc-300/25 dark:border-zinc-800/25
                                hover:bg-zinc-200 hover:dark:bg-zinc-800">
                                <div>{{ formatDate(log.date, 'date') }}</div>
                                <div>{{ log.event_type }}</div>
                                <div class="col-span-2">{{ log.model }}</div>
                            </li>
                        </ul>
                    </section>
                    <section class="bg-zinc-100 dark:bg-zinc-800 rounded relative">
                        <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400">Latest URL Events</h3>
                        <ul>
                            <li v-for="message in messages" class="grid grid-cols-3 items-center px-2 py-2 border-t border-zinc-300/25 dark:border-zinc-800/25
                                hover:bg-zinc-200 hover:dark:bg-zinc-800">
                                <div>{{ messageDate }}</div>
                                <div class="col-span-2">{{ message.route }}</div>
                            </li>
                        </ul>
                    </section>
                    <section v-if="$page.props.auth.user.role_id === 3"
                        class="lg:col-span-2 bg-zinc-100 dark:bg-zinc-800 rounded relative">
                        <h3 class=" text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4
                    py-2 rounded-t text-zinc-600 dark:text-zinc-400 mb-2">Latest Members (if ADMIN)</h3>
                        <ul>
                            <li v-for="member in users" class="grid grid-cols-3 px-2 pb-2">
                                <div>{{ member.name }}</div>
                                <div>{{ member.role.name }}</div>
                                <div>{{ formatDate(member.created_at) }}</div>
                            </li>
                        </ul>
                    </section>
                    <section class="grid grid-cols-3 gap-4 font-bold">
                        <div
                            class="bg-zinc-300/25 dark:bg-zinc-700/25 text-zinc-600 dark:text-zinc-400 uppercase flex flex-col items-center justify-center p-2 gap-2 rounded">
                            Database<span class="text-3xl text-zinc-700 dark:text-zinc-300">{{ numberOfLogs ?? 0
                            }}</span>
                        </div>
                        <div
                            class="bg-zinc-300/25 dark:bg-zinc-700/25 text-zinc-600 dark:text-zinc-400 uppercase flex flex-col items-center justify-center p-2 gap-2 rounded">
                            URL<span class="text-3xl text-zinc-700 dark:text-zinc-300">{{ numberOfMessages ?? 0 }}</span>
                        </div>
                    </section>
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
        numberOfLogs: Number,
        logs: Array,
        messages: Array,
        numberOfMessages: Number,
        messageDate: String
    },
    methods: {
        formatDate
    },
}
</script>
