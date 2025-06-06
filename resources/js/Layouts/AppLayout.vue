<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-gray-800 shadow-lg transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <h1 class="text-xl font-bold text-gray-800 dark:text-white">Gestion Territoires</h1>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <Link
                                href="/dashboard"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url === '/dashboard'
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Tableau de bord
                            </Link>

                            <Link
                                href="/departments"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/departments')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Départements
                            </Link>

                            <Link
                                href="/cities"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/cities')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Villes
                            </Link>

                            <Link
                                href="/map"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/map')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Carte
                            </Link>

                            <Link
                                href="/properties"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/properties')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Propriétés
                            </Link>

                            <Link
                                href="/scrapers/agences-en-limousin"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/properties/scraper')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Scraper
                            </Link>

                            <Link
                                v-if="$page.props.auth?.user?.roles?.some(role => role.name === 'admin')"
                                href="/users"
                                :class="[
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out',
                                    page.url.startsWith('/users')
                                        ? 'border-indigo-400 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-700'
                                        : 'border-transparent text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white hover:border-gray-300 focus:outline-none focus:text-gray-700 dark:focus:text-white focus:border-gray-300'
                                ]"
                            >
                                Utilisateurs
                            </Link>

                        </div>
                    </div>

                    <!-- Right side menu -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Theme Toggle -->
                        <button
                            @click="toggleTheme"
                            class="p-2 mr-4 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none transition-colors duration-200"
                            title="Basculer le thème"
                        >
                            <!-- Sun icon for light mode -->
                            <svg v-if="isDark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Moon icon for dark mode -->
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>
                        <div class="ml-3 relative">
                            <div v-if="$page.props.auth?.user" class="flex items-center">
                                <span class="text-sm text-gray-700 dark:text-gray-300 mr-4">
                                    {{ $page.props.auth.user.first_name }} {{ $page.props.auth.user.last_name }}
                                </span>
                                <div class="relative">
                                    <button
                                        @click="showUserMenu = !showUserMenu"
                                        class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:text-gray-700 dark:focus:text-white"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div v-if="showUserMenu" @click.away="showUserMenu = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50">
                                        <Link
                                            href="/account"
                                            class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                        >
                                            Mon compte
                                        </Link>
                                        <Link
                                            href="/logout"
                                            method="post"
                                            as="button"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600"
                                        >
                                            Déconnexion
                                        </Link>
                                    </div>
                                </div>
                            </div>
                            <div v-else>
                                <Link
                                    href="/login"
                                    class="text-sm text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:text-gray-700 dark:focus:text-white"
                                >
                                    Connexion
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-300 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-700 focus:text-gray-500 dark:focus:text-white transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden bg-white dark:bg-gray-800 transition-colors duration-300"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <Link
                        href="/departments"
                        :class="[
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out',
                            page.url.startsWith('/departments')
                                ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-800 focus:border-indigo-700'
                                : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300'
                        ]"
                    >
                        Départements
                    </Link>

                    <Link
                        href="/cities"
                        :class="[
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out',
                            page.url.startsWith('/cities')
                                ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-800 focus:border-indigo-700'
                                : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300'
                        ]"
                    >
                        Villes
                    </Link>

                    <Link
                        href="/map"
                        :class="[
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out',
                            page.url.startsWith('/map')
                                ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-800 focus:border-indigo-700'
                                : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300'
                        ]"
                    >
                        Carte
                    </Link>

                    <Link
                        v-if="$page.props.auth?.user?.roles?.some(role => role.name === 'admin')"
                        href="/users"
                        :class="[
                            'block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out',
                            page.url.startsWith('/users')
                                ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900 focus:outline-none focus:text-indigo-800 dark:focus:text-indigo-200 focus:bg-indigo-100 dark:focus:bg-indigo-800 focus:border-indigo-700'
                                : 'border-transparent text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-800 dark:focus:text-white focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300'
                        ]"
                    >
                        Utilisateurs
                    </Link>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useTheme } from '@/composables/useTheme'
import { useAuthStore } from '../stores/auth.js'

const showingNavigationDropdown = ref(false)
const showUserMenu = ref(false)
const page = usePage()
const authStore = useAuthStore()

// Theme management
const { isDark, toggleTheme } = useTheme()

// Check for token in flash data on mount (for redirects after login/register)
onMounted(() => {
    const token = page.props.flash?.token
    if (token) {
        authStore.setToken(token)
    }
})
</script>
