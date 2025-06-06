<template>
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow transition-colors duration-300">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Rechercher des propriétés
        </h3>

        <div class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rechercher une ville
                </label>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Tapez le nom d'une ville..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300"
                    @input="onSearchInput"
                />

                <!-- Dropdown des suggestions -->
                <div
                    v-if="showSuggestions && filteredCities.length > 0"
                    class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none transition-colors duration-300"
                >
                    <div
                        v-for="city in filteredCities.slice(0, 10)"
                        :key="city.id"
                        @click="selectCity(city)"
                        class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white text-gray-900 dark:text-white transition-colors duration-200"
                    >
                        <span class="block truncate">
                            {{ city.name }} ({{ city.postal_code }}) - {{ city.department.name }}
                        </span>
                        <span class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-300">
                            {{ formatNumber(city.population) }} habitants
                        </span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rayon (km)
                </label>
                <select
                    v-model="searchRadius"
                    class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300"
                    @change="onRadiusChange"
                >
                    <option value="5">5 km</option>
                    <option value="10">10 km</option>
                    <option value="20">20 km</option>
                    <option value="50">50 km</option>
                </select>
            </div>

            <div class="flex items-end">
                <button
                    @click="searchProperties"
                    :disabled="!selectedCity || loadingProperties"
                    class="px-4 py-2 bg-indigo-600 dark:bg-indigo-700 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-300"
                >
                    <span v-if="!loadingProperties">Rechercher</span>
                    <span v-else class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Recherche...
                    </span>
                </button>
            </div>
        </div>

        <!-- Ville sélectionnée -->
        <div v-if="selectedCity" class="mt-4 p-3 bg-indigo-50 dark:bg-indigo-900 rounded-md transition-colors duration-300">
            <p class="text-sm text-indigo-800 dark:text-indigo-200">
                <strong>Ville sélectionnée:</strong> {{ selectedCity.name }} ({{ selectedCity.postal_code }})
                - {{ formatNumber(selectedCity.population) }} habitants
            </p>
        </div>

        <!-- Résultats de recherche -->
        <div v-if="searchResults" class="mt-4">
            <div class="flex items-center justify-between mb-2">
                <h4 class="text-md font-medium text-gray-900 dark:text-white">
                    Propriétés trouvées
                </h4>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ searchResults.length }} résultat(s)
                </span>
            </div>

            <div v-if="searchResults.length === 0" class="text-sm text-gray-600 dark:text-gray-400">
                Aucune propriété trouvée dans cette zone. Les propriétés sans coordonnées géographiques ne peuvent pas être affichées sur la carte.
            </div>

            <div v-else class="space-y-2 max-h-40 overflow-y-auto">
                <div
                    v-for="property in searchResults"
                    :key="property.id"
                    class="p-2 bg-gray-50 dark:bg-gray-700 rounded text-sm transition-colors duration-300"
                >
                    <div class="font-medium text-gray-900 dark:text-white">{{ property.title }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ formatPrice(property.price) }} • {{ property.city_model?.name }} ({{ property.city_model?.postal_code }})
                    </div>
                    <div v-if="!property.latitude || !property.longitude" class="text-xs text-yellow-600 dark:text-yellow-400">
                        Position non géolocalisée
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth.js'

const props = defineProps({
    cities: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['properties-found', 'city-selected'])
const authStore = useAuthStore()

const searchQuery = ref('')
const selectedCity = ref(null)
const searchRadius = ref(10)
const showSuggestions = ref(false)
const loadingProperties = ref(false)
const searchResults = ref(null)

// Computed
const filteredCities = computed(() => {
    if (!searchQuery.value || searchQuery.value.length < 2) {
        return []
    }

    const query = searchQuery.value.toLowerCase()
    return props.cities.filter(city =>
        city.name.toLowerCase().includes(query) ||
        city.postal_code.includes(query)
    )
})

// Methods
const onSearchInput = () => {
    showSuggestions.value = searchQuery.value.length >= 2
    if (searchQuery.value.length < 2) {
        selectedCity.value = null
        searchResults.value = null
        emit('city-selected', null)
        emit('properties-found', [])
    }
}

const selectCity = (city) => {
    selectedCity.value = city
    searchQuery.value = `${city.name} (${city.postal_code})`
    showSuggestions.value = false
    emit('city-selected', city)
}

const onRadiusChange = () => {
    if (selectedCity.value) {
        searchProperties()
    }
}

const searchProperties = async () => {
    if (!selectedCity.value) return

    loadingProperties.value = true

    try {
        const params = {
            radius: searchRadius.value
        }

        // If city has coordinates, search by location
        if (selectedCity.value.latitude && selectedCity.value.longitude) {
            params.latitude = selectedCity.value.latitude
            params.longitude = selectedCity.value.longitude
        } else {
            // Otherwise search by city name
            params.city = selectedCity.value.name
        }

        const response = await axios.get('/api/properties/search', {
            params,
            headers: authStore.getAuthHeaders()
        })

        searchResults.value = response.data.properties
        emit('properties-found', response.data.properties)

    } catch (error) {
        console.error('Erreur lors de la recherche de propriétés:', error)
        searchResults.value = []
        emit('properties-found', [])
    } finally {
        loadingProperties.value = false
    }
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('fr-FR').format(number)
}

const formatPrice = (price) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        maximumFractionDigits: 0
    }).format(price)
}

// Close suggestions when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showSuggestions.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>
