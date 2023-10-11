<template>
    <app-layout title="Error Page">
        <section class="grid items-center max-w-5xl mx-auto px-12">
            <div class="py-12 dark:bg-slate-800/50 bg-slate-400/50 rounded-lg text-center shadow">
                <div class="max-w-7xl mx-auto px-4 sm:px-20 lg:px-32">
                    <h3 class="font-semibold font-heading text-3xl text-zinc-800 leading-tight dark:text-zinc-100">
                        <span class="uppercase text-sky-400 font-heading text-5xl">{{ error.status
                        }}</span> <br />
                        You have encountered an error!
                    </h3>
                    <div class="dark:text-zinc-400 text-zinc-700 mt-8">
                        <h4>{{ error.message.header }}</h4>
                        <p>{{ error.message.info }}</p>
                    </div>
                </div>
            </div>
        </section>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';
export default {
    props: {
        error: Object
    },
    components: {
        AppLayout
    }
}
</script>
