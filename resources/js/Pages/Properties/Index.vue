<template>
    <AppLayout title="Propriétés">
        <div class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Propriétés immobilières</h1>
                <button
                    @click="openModal()"
                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Ajouter une propriété
                </button>
            </div>

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
                <PropertyCard
                    v-for="property in properties.data"
                    :key="property.id"
                    :property="property"
                    @edit="openModal"
                    @delete="handleDeleted"
                />
            </div>

            <!-- Pagination -->
            <div v-if="properties.data && properties.data.length > 0" class="mt-8">
                <Pagination :links="properties.links" />
            </div>
        </div>

        <!-- Modal -->
        <PropertyForm
            v-if="showModal"
            :property="selectedProperty"
            :cities="cities"
            @close="closeModal"
            @saved="handleSaved"
        />
    </AppLayout>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/components/common/Pagination.vue';
import PropertyCard from '@/components/properties/PropertyCard.vue';
import PropertyForm from '@/components/properties/PropertyForm.vue';
import { debounce } from 'lodash';

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

// Reactive data
const properties = ref(props.properties || { data: [], links: [] })
const cities = ref(props.cities || [])
const loading = ref(false)
const showModal = ref(false)
const selectedProperty = ref(null)


const filters = reactive({
    search: props.filters.search || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    min_surface: props.filters.min_surface || '',
    max_surface: props.filters.max_surface || '',
    rooms: props.filters.rooms || '',
    property_type: props.filters.property_type || '',
    transaction_type: props.filters.transaction_type || '',
});



// Data fetching
const fetchProperties = async () => {
    try {
        loading.value = true
        const cleanFilters = Object.fromEntries(
            Object.entries(filters).filter(([_, value]) => value !== '' && value !== null)
        )

        // Use Inertia router for navigation with filters
        router.get('/properties', cleanFilters, {
            preserveState: true,
            preserveScroll: true,
            only: ['properties'],
            onSuccess: (page) => {
                properties.value = page.props.properties
            },
            onFinish: () => {
                loading.value = false
            }
        })
    } catch (error) {
        console.error('Error fetching properties:', error)
        loading.value = false
    }
}


// Filter operations
const applyFilters = () => fetchProperties()
const debouncedSearch = debounce(applyFilters, 500)

const resetFilters = () => {
    Object.keys(filters).forEach(key => filters[key] = '')
    applyFilters()
}

const openModal = (property = null) => {
    selectedProperty.value = property
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedProperty.value = null
}

const handleSaved = () => {
    closeModal()
    // Force a complete reload to refresh the properties list
    window.location.reload()
}

const handleDeleted = () => {
    // Force a complete reload after deletion
    window.location.reload()
}

// Fetch initial data
onMounted(() => {
    // Initial data is already loaded via props
});
</script>
