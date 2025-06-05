<template>
    <AppLayout title="Scraper de Propriétés">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Scrapers de Propriétés Immobilières</h1>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 transition-colors duration-300">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Scrapers disponibles</h2>
                
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Choisissez un scraper pour extraire des données de propriétés immobilières depuis différentes sources.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Agences en Limousin Scraper -->
                    <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700 transition-colors duration-300">
                        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">Agences en Limousin</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Extraire des propriétés depuis le site agencesenlimousin.com
                        </p>
                        <a
                            href="/scrapers/agences-en-limousin"
                            class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-700 dark:hover:bg-blue-800 transition-colors inline-block"
                        >
                            Accéder au scraper
                        </a>
                    </div>

                    <!-- Placeholder for future scrapers -->
                    <div class="border border-gray-300 dark:border-gray-600 border-dashed rounded-lg p-4 bg-gray-50 dark:bg-gray-700 opacity-50 transition-colors duration-300">
                        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">SeLoger</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Scraper pour SeLoger (à venir)
                        </p>
                        <button
                            disabled
                            class="bg-gray-400 dark:bg-gray-500 text-white px-4 py-2 rounded cursor-not-allowed transition-colors duration-300"
                        >
                            Bientôt disponible
                        </button>
                    </div>

                    <div class="border border-gray-300 dark:border-gray-600 border-dashed rounded-lg p-4 bg-gray-50 dark:bg-gray-700 opacity-50 transition-colors duration-300">
                        <h3 class="text-lg font-medium mb-2 text-gray-900 dark:text-white">LeBonCoin</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            Scraper pour LeBonCoin (à venir)
                        </p>
                        <button
                            disabled
                            class="bg-gray-400 dark:bg-gray-500 text-white px-4 py-2 rounded cursor-not-allowed transition-colors duration-300"
                        >
                            Bientôt disponible
                        </button>
                    </div>
                </div>

                <!-- Stats section -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-white">Statistiques récentes</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-300">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ stats.totalProperties || 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Propriétés totales</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-300">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ stats.activeProperties || 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Propriétés actives</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg transition-colors duration-300">
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ stats.citiesCount || 0 }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Villes référencées</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const stats = ref({
    totalProperties: 0,
    activeProperties: 0,
    citiesCount: 0
})

// This would typically fetch real stats from an API
onMounted(async () => {
    // Placeholder for stats fetching
    // In a real implementation, you would fetch from an API endpoint
    stats.value = {
        totalProperties: 150,
        activeProperties: 142,
        citiesCount: 89
    }
})
</script>