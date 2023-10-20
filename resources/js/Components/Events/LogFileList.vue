<template>
    <section class="bg-zinc-100 dark:bg-zinc-800  rounded relative row-span-2 mb-6">
        <h3 class="text-lg uppercase font-heading font-bold bg-zinc-300/25 dark:bg-zinc-700/25 px-4 py-2 rounded-t
        text-zinc-600 dark:text-zinc-400 mb-2">Available Log Files</h3>
        <ul class="p-4">
            <li v-for="(file, index) in fileList" :key="file" class="text-zinc-900 dark:text-zinc-100 flex gap-4 py-1"
                :class="{ 'hidden': index >= 5 && !isExtended }">
                <span class="flex-1">{{
                    file.name.substring(file.name.lastIndexOf('/') + 1,
                        file.name.length)
                }}</span>
                <secondary-button>
                    <Link :href="`/event/files?file=${file.name.substring(file.name.lastIndexOf('/') + 1,
                                file.name.length)}`">View</Link>
                </secondary-button>
                <secondary-button :disabled="file.backup" @click="$emit('backupFile', file.name.substring(file.name.lastIndexOf('/') + 1,
                    file.name.length))">Backup</secondary-button>
                <danger-button @click="$emit('deleteFile', file.name.substring(file.name.lastIndexOf('/') + 1,
                    file.name.length))">Delete</danger-button>
            </li>
        </ul>
        <div v-if="files.length > 5" class="absolute right-2 bottom-0" @click="toggleExtendList"><i class="fa fa-solid fa-chevron-down text-xl
            text-zinc-900 bg:text-zinc-100 transition-transform duration-300 ease-in-out"
                :class="{ 'rotate-180': isExtended }"></i>
        </div>
    </section>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import DangerButton from '../DangerButton.vue';
import SecondaryButton from '../SecondaryButton.vue';
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
            return this.isExtended ? this.files : this.files.slice(0, 5);
        }
    },
    methods: {
        toggleExtendList() {
            this.isExtended = !this.isExtended;
        }
    },
    components: {
        Link,
        DangerButton,
        SecondaryButton
    }
}
</script>
