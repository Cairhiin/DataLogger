<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue'
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import DarkModeSwitch from '@/Components/Custom/DarkModeSwitch.vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const showingDarkMode = ref(false);

const logout = () => {
    router.post(route('logout'));
};

const changeMode = () => {
    showingDarkMode.value = !showingDarkMode.value;
    localStorage.setItem('color-theme', showingDarkMode.value);

    window.dispatchEvent(new CustomEvent('mode-changed', {
        detail: {
            storage: showingDarkMode.value
        }
    }));
}

onMounted(() => {
    if (localStorage.getItem('color-theme') === 'true' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        showingDarkMode.value = true;
    } else {
        showingDarkMode.value = false;
    }
});
</script>

<template>
    <div :class="{ 'dark': showingDarkMode }" id="anchor">

        <Head :title="title" />

        <Banner />

        <div class="min-h-screen bg-white dark:bg-gradient-to-b dark:from-zinc-950 dark:to-zinc-900">
            <nav class="bg-white dark:bg-transparent">
                <!-- Primary Navigation Menu -->
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                <h1 class="text-3xl text-sky-400 font-bold uppercase font-heading">Event <span
                                        class="text-slate-500">Logger</span>
                                </h1>
                                </Link>
                            </div>
                        </div>

                        <div class="hidden sm:flex sm:items-center sm:ml-6 gap-4">
                            <!-- Settings Dropdown -->
                            <div class="ml-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos"
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                :src="$page.props.auth.user.profile_photo_url"
                                                :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                                                text-gray-500 bg-white  hover:text-gray-700 focus:outline-none
                                                dark:text-zinc-100 dark:bg-zinc-900 dark:hover:text-slate-400 dark:focus:bg-zinc-900 dark:active:bg-zinc-900
                                                transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400 dark:text-zinc-500">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures"
                                            :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200 dark:border-zinc-700" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                            <DarkModeSwitch @change-mode="changeMode" :showingDarkMode="showingDarkMode" />
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                                @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path
                                        :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }"
                    class="sm:hidden">
                    <div class="pt-2 pb-3 space-y-1">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="pt-4 pb-1 border-t border-gray-200">
                        <div class="flex items-center px-4">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 mr-3">
                                <img class="h-10 w-10 rounded-full object-cover"
                                    :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>

                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')"
                                :active="route().current('api-tokens.index')">
                                API Tokens
                            </ResponsiveNavLink>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-transparent">
                <div class="max-w-7xl mx-auto py-6 px-6  dark:text-zinc-200 text-zinc-800 font-heading
                text-2xl">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto py-6 lg:px-8 lg:flex lg:max-w-full">
                <!-- Sidebar -->
                <nav class="w-40"><!-- Navigation Links -->
                    <div class="hidden sm:-my-px sm:flex sm:flex-col">
                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            <span class="fa fa-solid fa-gauge mr-3 opacity-50 transition-all duration-500 w-4"></span>
                            Dashboard
                        </NavLink>
                        <NavLink :href="route('event.logs.index')" :active="route().current('event.logs.index')">
                            <span class="fa fa-solid fa-database mr-3 opacity-50 transition-all duration-500 w-4"></span>
                            Database Events
                        </NavLink>
                        <NavLink :href="route('event.messages.index')" :active="route().current('event.messages.index')">
                            <span class="fa fa-solid fa-link mr-3 opacity-50 transition-all duration-500 w-4"></span> Url
                            Events
                        </NavLink>
                    </div>
                </nav>
                <div class="flex-1 px-8">
                    <slot />
                </div>

            </main>
        </div>
    </div>
</template>
