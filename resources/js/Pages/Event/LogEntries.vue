<template>
    <ul>
        <li v-for="log in logEntries" :key="log.id">{{ log.user_email }}</li>
    </ul>
</template>

<script>
export default {
    props: {
        logEntries: Array
    }
}
</script>
