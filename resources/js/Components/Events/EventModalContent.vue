<template>
    <div class="border-t border-zinc-300 dark:border-zinc-700 p-4 flex justify-between items-center">
        <div class="text-red-600 font-bold text-sm uppercase"><span
                :class="{ 'fa fa-solid fa-warning': error }"></span>&nbsp;&nbsp;{{
                    error }}
        </div>
        <div class="flex justify-end  gap-6">
            <secondary-button @click="$emit('hideDetails')">
                <span class="text-base fa fa-solid fa-xmark"></span>
                &nbsp;&nbsp;Close</secondary-button>
            <secondary-button @click="$emit('decryptData')">
                <span :class="{ 'text-sm fa fa-solid fa-spinner fa-spin': isLoading }"></span>
                <span :class="{ 'text-sm fa fa-solid fa-key': !isLoading }"></span>
                &nbsp;&nbsp;Decrypt</secondary-button>
            <danger-button @click="$emit('deleteLog')">Delete</danger-button>
        </div>
    </div>
</template>

<script>
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

export default {
    components: {
        SecondaryButton,
        PrimaryButton,
        DangerButton
    },
    props: {
        error: String,
        isLoading: Boolean
    }
}
</script>
