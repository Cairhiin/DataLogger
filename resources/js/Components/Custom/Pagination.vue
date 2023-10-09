<template>
    <div v-if="links.length > 3">
        <div class="flex flex-wrap -mb-1 justify-center mt-4">
            <template v-for="(link, key) in links">
                <div v-if="link.url === null" class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-400 dark:text-zinc-500"
                    v-html="link.label" />
                <Link v-else
                    class="mr-1 mb-1 px-4 py-3 text-sm leading-4  focus:border-indigo-500 focus:text-indigo-500 dark:text-zinc-100"
                    :href="link.url" v-html="link.label" />
            </template>
        </div>
    </div>
</template>

<script>
import { Link } from "@inertiajs/vue3";
export default {
    props: {
        links: Array,
    },
    components: {
        Link
    }
}
</script>
