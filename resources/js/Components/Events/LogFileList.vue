<template>
    <section class="bg-zinc-100 dark:bg-zinc-800 p-4 rounded mb-4 overflow-hidden relative">
        <h3 class="text-lg uppercase font-heading font-bold
        text-slate-600 dark:text-zinc-300">Available Log Files</h3>
        <ol class="list-decimal px-4">
            <li v-for="file in fileList" :key="file">
                <Link :href="`/event/messages?file=${file}`">{{ file.substring(file.lastIndexOf('/') + 1,
                    file.length)
                }}</Link>
            </li>
        </ol>
        <div class="absolute right-2 bottom-0" @click="toggleExtendList"><i class="fa fa-solid fa-chevron-down text-xl"></i>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default {
    data() {
        return {
            isExtended: false
        }
    },
    props: {
        files: Array
    },
    computed: {
        fileList() {
            return this.isExtended ? this.files : this.files.slice(0, 1);
        }
    },
    methods: {
        toggleExtendList() {
            this.isExtended = !this.isExtended;
        }
    },
    components: {
        Link
    }
}
</script>
