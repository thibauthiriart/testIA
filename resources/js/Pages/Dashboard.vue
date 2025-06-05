<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Tableau de bord</h1>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Carte Départements -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-600 dark:text-blue-400 text-sm font-medium">Départements</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ loading ? '...' : stats.departments_count }}</p>
                            </div>
                            <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
                            </svg>
                        </div>
                        <Link href="/departments" class="mt-4 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium inline-flex items-center">
                            Voir tous les départements
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Carte Villes -->
                    <div class="bg-green-50 dark:bg-green-900/20 p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-600 dark:text-green-400 text-sm font-medium">Villes</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ loading ? '...' : stats.cities_count }}</p>
                            </div>
                            <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <Link href="/cities" class="mt-4 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-sm font-medium inline-flex items-center">
                            Voir toutes les villes
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Carte Population totale -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 p-6 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-600 dark:text-purple-400 text-sm font-medium">Population totale</p>
                                <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ loading ? '...' : formatNumber(stats.total_population) }}</p>
                            </div>
                            <svg class="w-12 h-12 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Actions rapides</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Link href="/departments" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Ajouter un département</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Créer un nouveau département</p>
                            </div>
                        </Link>

                        <Link href="/cities" class="flex items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Ajouter une ville</p>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Créer une nouvelle ville</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useAuthStore } from '../stores/auth.js'
import { useApi } from '../composables/useApi.js'
import { onMounted, ref } from 'vue'

const authStore = useAuthStore()
const page = usePage()
const api = useApi()

const stats = ref({
    departments_count: 0,
    cities_count: 0,
    total_population: 0
})

const loading = ref(true)

const fetchStats = async () => {
    try {
        const data = await api.get('/api/dashboard/stats')
        stats.value = data
    } catch (error) {
        console.error('Error fetching stats:', error)
    } finally {
        loading.value = false
    }
}

// Check for token in flash data on mount and fetch stats
onMounted(() => {
    const token = page.props.flash?.token
    if (token) {
        authStore.setToken(token)
    }

    // Fetch stats via API
    fetchStats()
})

const formatNumber = (num) => {
    return new Intl.NumberFormat('fr-FR').format(num)
}
</script>
