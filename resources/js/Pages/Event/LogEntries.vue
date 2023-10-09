<template>
    <div>
        <div class="grid grid-cols-6 gap-8 p-2 bg-zinc-900 text-zinc-100 font-bold">
            <div>IP</div>
            <div>Changed</div>
            <div>Original</div>
            <div>Route</div>
            <div>Event</div>
        </div>
        <div v-for="log in logEntries.data" :key="log.id" class="grid grid-cols-6 gap-8 p-2 odd:bg-sky-300 even:bg-sky-200">
            <div>
                {{ log.ip_address }}
            </div>
            <div>
                <ul>
                    <li v-for="(value, key, index) in log.new_data" :key="key" :class="{ hidden: index > 0 }">{{ `${key}:
                                            ${value}` }}</li>
                </ul>
            </div>
            <div>
                <ul>
                    <li v-for="(value, key, index) in log.original_data" :key="key" :class="{ hidden: index > 0 }">{{
                        `${key}: ${value}` }}</li>
                </ul>
            </div>
            <div>
                {{ log.route }}
            </div>
            <div>
                {{ log.event_type }}
            </div>
            <div>
                <button>Details</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        logEntries: Object
    }
}
</script>
