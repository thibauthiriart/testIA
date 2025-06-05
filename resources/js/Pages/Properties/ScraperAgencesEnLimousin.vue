<template>
    <AppLayout title="Scraper - Agences en Limousin">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                     Scraper
                </h1>

            </div>

            <!-- Search Form -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        Paramètres de recherche
                    </h2>
                    <p class="text-blue-100">
                        Recherchez et extrayez des propriétés immobilières depuis agencesenlimousin.com
                    </p>
                </div>

                <form @submit.prevent="scrapeProperties" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <!-- Département -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                 Département
                            </label>
                            <select
                                v-model="form.department"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                required
                            >
                                <option value="">Sélectionner un département</option>
                                <option v-for="(name, code) in departments" :key="code" :value="code">
                                    {{ name }}
                                </option>
                            </select>
                        </div>

                        <!-- Ville -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                 Ville
                            </label>
                            <input
                                v-model="form.city"
                                type="text"
                                placeholder="Nom de la ville (optionnel)"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                            />
                        </div>

                        <!-- Type de transaction -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                 Type de transaction
                            </label>
                            <select
                                v-model="form.transaction_type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                required
                            >
                                <option value="1"> Vente</option>
                                <option value="2"> Location</option>
                            </select>
                        </div>

                        <!-- Prix -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                 Prix (€)
                            </label>
                            <div class="flex space-x-2">
                                <input
                                    v-model="form.min_price"
                                    type="number"
                                    placeholder="Min"
                                    class="w-1/2 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                />
                                <input
                                    v-model="form.max_price"
                                    type="number"
                                    placeholder="Max"
                                    class="w-1/2 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                />
                            </div>
                        </div>

                        <!-- Surface -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                 Surface (m²)
                            </label>
                            <div class="flex space-x-2">
                                <input
                                    v-model="form.min_surface"
                                    type="number"
                                    placeholder="Min"
                                    class="w-1/2 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                />
                                <input
                                    v-model="form.max_surface"
                                    type="number"
                                    placeholder="Max"
                                    class="w-1/2 px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center">
                        <button
                            type="submit"
                            :disabled="loading"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-800 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-105 transition-all duration-200 shadow-lg"
                        >
                            <span v-if="!loading" class="flex items-center">
                                 Lancer le scraping
                            </span>
                            <span v-else class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Scraping en cours...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="mb-8">
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-medium text-red-800 dark:text-red-200">
                                Erreur lors du scraping
                            </h3>
                            <p class="mt-1 text-red-700 dark:text-red-300">
                                {{ error }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results -->
            <div v-if="results && results.properties" class="mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            ✅ Résultats trouvés
                            <span class="ml-2 bg-white/20 px-3 py-1 rounded-full text-sm">
                                {{ results.properties.length }} biens
                            </span>
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div
                                v-for="property in results.properties"
                                :key="property.detail_url"
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden hover:shadow-lg transform hover:scale-105 transition-all duration-200 border border-gray-200 dark:border-gray-600"
                            >
                                <!-- Image -->
                                <div class="relative h-48 bg-gray-200 dark:bg-gray-600">
                                    <img
                                        v-if="property.image_url"
                                        :src="property.image_url"
                                        :alt="property.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div v-else class="flex items-center justify-center h-full">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="p-4">
                                    <h3 class="font-semibold text-lg mb-3 text-gray-900 dark:text-white line-clamp-2">
                                        {{ property.title }}
                                    </h3>

                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <span class="font-medium text-green-600 dark:text-green-400 text-lg">
                                                {{ formatPrice(property.price) }}
                                            </span>
                                        </div>

                                        <div v-if="property.surface" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                            </svg>
                                            {{ property.surface }} m²
                                        </div>

                                        <div v-if="property.location" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ property.location }}
                                        </div>
                                    </div>

                                    <p v-if="property.description" class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                        {{ truncateText(property.description, 120) }}
                                    </p>

                                    <a
                                        :href="property.detail_url"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200"
                                    >
                                        Voir les détails
                                        <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Load More Button -->
                        <div v-if="results.pagination && results.pagination.has_next_page" class="mt-8 text-center">
                            <button
                                @click="loadNextPage"
                                :disabled="loading"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-lg"
                            >
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Charger plus de résultats
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import axios from 'axios'

const props = defineProps({
    departments: Object
})

const form = ref({
    department: '',
    city: '',
    transaction_type: '1',
    min_price: '',
    max_price: '',
    min_surface: '',
    max_surface: '',
    page: 1
})

const loading = ref(false)
const error = ref('')
const results = ref(null)

const scrapeProperties = async () => {
    loading.value = true
    error.value = ''
    form.value.page = 1

    try {
        const response = await axios.post('/scrapers/agences-en-limousin/scrape', form.value)
        results.value = response.data.data
    } catch (err) {
        error.value = err.response?.data?.message || 'Une erreur est survenue lors du scraping'
        console.error('Scraping error:', err)
    } finally {
        loading.value = false
    }
}

const loadNextPage = async () => {
    loading.value = true
    error.value = ''
    form.value.page++

    try {
        const response = await axios.post('/scrapers/agences-en-limousin/scrape', form.value)
        results.value.properties.push(...response.data.data.properties)
        results.value.pagination = response.data.data.pagination
    } catch (err) {
        error.value = err.response?.data?.message || 'Une erreur est survenue lors du scraping'
        console.error('Next page error:', err)
        form.value.page--
    } finally {
        loading.value = false
    }
}

const formatPrice = (price) => {
    if (!price) return 'Prix non communiqué'
    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price)
}

const truncateText = (text, maxLength) => {
    if (!text || text.length <= maxLength) return text
    return text.substring(0, maxLength) + '...'
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
