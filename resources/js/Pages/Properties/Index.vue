<template>
    <AppLayout title="Propriétés">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-900 dark:text-white">Propriétés immobilières</h1>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-8 transition-colors duration-300">
                <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Filtres</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Recherche</label>
                        <input
                            v-model="filters.search"
                            @input="debouncedSearch"
                            type="text"
                            placeholder="Titre, ville, code postal..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Ville</label>
                        <div class="relative">
                            <input
                                v-model="citySearch"
                                type="text"
                                placeholder="Rechercher une ville..."
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                @input="onCitySearchInput"
                                @focus="showCitySuggestions = true"
                                @blur="hideCitySuggestions"
                            />

                            <!-- Dropdown des suggestions -->
                            <div
                                v-if="showCitySuggestions && filteredCities.length > 0"
                                class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none transition-colors duration-300"
                            >
                                <div
                                    v-for="city in filteredCities.slice(0, 10)"
                                    :key="city.id"
                                    @mousedown.prevent="selectCity(city)"
                                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-indigo-600 hover:text-white text-gray-900 dark:text-white transition-colors duration-200"
                                >
                                    <span class="block truncate">
                                        {{ city.name }} ({{ city.postal_code }})
                                    </span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-300">
                                        {{ city.department?.name }}
                                    </span>
                                </div>
                            </div>

                            <!-- Ville sélectionnée -->
                            <div v-if="selectedCity" class="mt-2 p-2 bg-indigo-50 dark:bg-indigo-900 rounded-md">
                                <p class="text-sm text-indigo-800 dark:text-indigo-200">
                                    <strong>Ville sélectionnée:</strong> {{ selectedCity.name }} ({{ selectedCity.postal_code }})
                                    <button
                                        @click="clearCitySelection"
                                        class="ml-2 text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200"
                                    >
                                        ✕
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Type de transaction</label>
                        <select
                            v-model="filters.transaction_type"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Tous</option>
                            <option value="sale">Vente</option>
                            <option value="rent">Location</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Type de bien</label>
                        <select
                            v-model="filters.property_type"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Tous</option>
                            <option value="apartment">Appartement</option>
                            <option value="house">Maison</option>
                            <option value="villa">Villa</option>
                            <option value="studio">Studio</option>
                            <option value="land">Terrain</option>
                            <option value="parking">Parking</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Prix min (€)</label>
                        <input
                            v-model.number="filters.min_price"
                            @change="applyFilters"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Prix max (€)</label>
                        <input
                            v-model.number="filters.max_price"
                            @change="applyFilters"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Surface min (m²)</label>
                        <input
                            v-model.number="filters.min_surface"
                            @change="applyFilters"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Surface max (m²)</label>
                        <input
                            v-model.number="filters.max_surface"
                            @change="applyFilters"
                            type="number"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Nombre de pièces</label>
                        <select
                            v-model.number="filters.rooms"
                            @change="applyFilters"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white transition-colors duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Tous</option>
                            <option v-for="i in 10" :key="i" :value="i">{{ i }}</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex gap-2">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    >
                        Réinitialiser
                    </button>
                </div>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="text-gray-500 mt-4">Chargement des propriétés...</p>
            </div>

            <!-- Results -->
            <div v-else-if="properties.data && properties.data.length === 0" class="text-center py-8">
                <p class="text-gray-500">Aucune propriété trouvée</p>
            </div>

            <div v-else-if="properties.data && properties.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="property in properties.data"
                    :key="property.id"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow"
                >
                    <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                        <img
                            v-if="property.images && property.images.length > 0"
                            :src="property.images[0]"
                            :alt="property.title"
                            class="w-full h-48 object-cover"
                        />
                        <div v-else class="w-full h-48 bg-gray-300 flex items-center justify-center">
                            <span class="text-gray-500">Pas d'image</span>
                        </div>
                    </div>

                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">{{ property.title }}</h3>

                        <div class="text-2xl font-bold text-blue-600 mb-2">
                            {{ formatPrice(property.price) }}
                            <span v-if="property.transaction_type === 'rent'" class="text-sm">/mois</span>
                        </div>

                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div v-if="property.cityModel">
                                📍 {{ property.cityModel.name }} ({{ property.cityModel.postal_code }})
                            </div>
                            <div v-if="property.surface">
                                📐 {{ property.surface }} m²
                            </div>
                            <div v-if="property.rooms">
                                🏠 {{ property.rooms }} pièce{{ property.rooms > 1 ? 's' : '' }}
                            </div>
                        </div>

                        <div class="mt-4 flex justify-between items-center">
                            <span class="text-xs text-gray-500">
                                {{ formatDate(property.scraped_at) }}
                            </span>
                            <a
                                :href="property.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-blue-600 hover:text-blue-800 text-sm"
                            >
                                Voir sur {{ property.source }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="properties.data && properties.data.length > 0" class="mt-8">
                <Pagination :links="properties.links" />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onUnmounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/components/Pagination.vue';
import { useApi } from '../../composables/useApi.js';
import { debounce } from 'lodash';

const api = useApi()

// Reactive data
const properties = ref({ data: [], links: [] })
const cities = ref([])
const loading = ref(true)

// City search variables
const citySearch = ref('')
const selectedCity = ref(null)
const showCitySuggestions = ref(false)

const props = defineProps({
    properties: {
        type: Object,
        default: () => ({ data: [], links: [] }),
    },
    cities: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    sort: {
        type: Object,
        default: () => ({ field: 'scraped_at', direction: 'desc' }),
    },
});

const filters = reactive({
    search: props.filters.search || '',
    city_id: props.filters.city_id || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    min_surface: props.filters.min_surface || '',
    max_surface: props.filters.max_surface || '',
    rooms: props.filters.rooms || '',
    property_type: props.filters.property_type || '',
    transaction_type: props.filters.transaction_type || '',
});

// Computed
const filteredCities = computed(() => {
    if (!citySearch.value || citySearch.value.length < 2) {
        return []
    }

    const query = citySearch.value.toLowerCase()
    return cities.value.filter(city =>
        city.name.toLowerCase().includes(query) ||
        city.postal_code.includes(query)
    )
})

// Helper function to update filter and refresh
const updateFilter = (key, value) => {
    filters[key] = value
    applyFilters()
}

// Helper function to clear city selection
const resetCitySelection = () => {
    selectedCity.value = null
    citySearch.value = ''
    showCitySuggestions.value = false
}

// City search methods
const onCitySearchInput = () => {
    showCitySuggestions.value = citySearch.value.length >= 2
    if (citySearch.value.length < 2) {
        resetCitySelection()
        updateFilter('city_id', '')
    }
}

const selectCity = (city) => {
    selectedCity.value = city
    citySearch.value = `${city.name} (${city.postal_code})`
    showCitySuggestions.value = false
    updateFilter('city_id', city.id)
}

const clearCitySelection = () => {
    resetCitySelection()
    updateFilter('city_id', '')
}

const hideCitySuggestions = () => {
    setTimeout(() => showCitySuggestions.value = false, 200)
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showCitySuggestions.value = false
    }
}

// Data fetching
const fetchProperties = async () => {
    try {
        loading.value = true
        const cleanFilters = Object.fromEntries(
            Object.entries(filters).filter(([_, value]) => value !== '' && value !== null)
        )
        console.log('Fetching properties with filters:', cleanFilters)
        const data = await api.get('/api/properties', cleanFilters)
        console.log('Properties received:', data)
        properties.value = data
    } catch (error) {
        console.error('Error fetching properties:', error)
        properties.value = { data: [], links: [] }
    } finally {
        loading.value = false
    }
}

const fetchCities = async () => {
    try {
        const data = await api.get('/api/cities')
        console.log('Cities received:', data)
        cities.value = data.data || []
    } catch (error) {
        console.error('Error fetching cities:', error)
        cities.value = []
    }
}

// Filter operations
const applyFilters = () => fetchProperties()
const debouncedSearch = debounce(applyFilters, 500)

const resetFilters = () => {
    Object.keys(filters).forEach(key => filters[key] = '')
    resetCitySelection()
    applyFilters()
}

const formatPrice = (price) => {
    if (!price) return 'Prix non renseigné';
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

// Fetch initial data
onMounted(() => {
    // Charger toutes les données via API pour la cohérence
    fetchProperties()
    fetchCities()
    document.addEventListener('click', handleClickOutside)
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
});
</script>
