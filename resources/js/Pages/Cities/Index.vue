<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestion des Villes</h1>
                <button
                    @click="openModal()"
                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Ajouter une ville
                </button>
            </div>

            <DataTable>
                <template #headers>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex flex-col">
                                    <SortableHeader
                                        label="Nom"
                                        :is-active="currentSort === 'name'"
                                        :direction="currentDirection"
                                        @sort="handleSort('name')"
                                    />
                                    <SearchInput
                                        v-model="search"
                                        class="mt-2"
                                        @input="handleSearch"
                                    />
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex flex-col">
                                    <SortableHeader
                                        label="Population"
                                        :is-active="currentSort === 'population'"
                                        :direction="currentDirection"
                                        @sort="handleSort('population')"
                                    />
                                    <div class="flex items-center mt-2 space-x-1">
                                        <select
                                            v-model="populationOperator"
                                            class="px-1 py-1 text-sm font-normal normal-case border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400"
                                            @change="handlePopulationFilter"
                                        >
                                            <option value="">--</option>
                                            <option value="gt">&gt;</option>
                                            <option value="lt">&lt;</option>
                                            <option value="eq">=</option>
                                        </select>
                                        <input
                                            v-model.number="populationValue"
                                            type="number"
                                            placeholder="0"
                                            class="w-20 px-2 py-1 text-sm font-normal normal-case border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400"
                                            @input="handlePopulationFilter"
                                            min="0"
                                        />
                                    </div>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <div class="flex flex-col">
                                    <SortableHeader
                                        label="Département"
                                        :is-active="currentSort === 'department'"
                                        :direction="currentDirection"
                                        @sort="handleSort('department')"
                                    />
                                    <div class="relative mt-2">
                                        <SearchInput
                                            v-model="departmentSearch"
                                            @input="handleDepartmentSearch"
                                            @focus="showSuggestions = true"
                                            @blur="hideSuggestions"
                                        />
                                        <div v-if="showSuggestions && filteredDepartments.length > 0" class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-auto">
                                            <button
                                                v-for="dept in filteredDepartments"
                                                :key="dept.id"
                                                @mousedown.prevent="selectDepartment(dept)"
                                                class="w-full text-left px-3 py-2 text-sm text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 focus:bg-gray-100 dark:focus:bg-gray-600 focus:outline-none"
                                            >
                                                {{ dept.name }} ({{ dept.code }})
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                <button
                                    @click="resetSort"
                                    class="flex items-center space-x-1 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 focus:outline-none"
                                    title="Réinitialiser le tri"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-xs">Réinitialiser</span>
                                </button>
                            </th>
                </template>
                <template #body>
                        <tr v-for="city in cities.data" :key="city.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ city.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ city.population.toLocaleString('fr-FR') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ city.department.name }} ({{ city.department.code }})
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button
                                    @click="openModal(city)"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                                >
                                    Modifier
                                </button>
                                <button
                                    @click="confirmDelete(city)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                >
                                    Supprimer
                                </button>
                            </td>
                            <td class="px-6 py-4"></td>
                        </tr>
                </template>
            </DataTable>

            <!-- Pagination et Items par page -->
            <div class="mt-6 flex justify-between items-center">
                <!-- Sélecteur d'items par page -->
                <ItemsPerPage
                    v-model="perPage"
                    @update:modelValue="handlePerPageChange"
                />

                <!-- Pagination -->
                <Pagination
                    :links="cities.links"
                    :only="['cities']"
                    @page-change="handlePageChange"
                />

                <!-- Espace vide pour équilibrer la mise en page -->
                <div v-if="!(cities.links && cities.links.length > 3)" class="w-32"></div>
            </div>
        </div>

        <!-- Modal -->
        <CityForm
            v-if="showModal"
            :city="selectedCity"
            :departments="departments"
            @close="closeModal"
            @saved="handleSaved"
        />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import CityForm from './CityForm.vue'
import DataTable from '@/components/common/DataTable.vue'
import SortableHeader from '@/components/common/SortableHeader.vue'
import SearchInput from '@/components/common/SearchInput.vue'
import Pagination from '@/components/common/Pagination.vue'
import ItemsPerPage from '@/components/common/ItemsPerPage.vue'
import { useApi } from '../../composables/useApi.js'
import Swal from 'sweetalert2'

const api = useApi()

// Reactive data
const cities = ref({ data: [], links: [] })
const departments = ref([])
const loading = ref(true)

const props = defineProps({
    filters: Object
})

const showModal = ref(false)
const selectedCity = ref(null)
const search = ref(props.filters?.search || '')
const departmentSearch = ref(props.filters?.department_search || '')
const selectedDepartmentId = ref(props.filters?.department_id || '')
const showSuggestions = ref(false)
const perPage = ref(props.filters?.per_page || 10)
const populationOperator = ref(props.filters?.population_operator || '')
const populationValue = ref(props.filters?.population_value || '')
const currentSort = ref(props.filters?.sort || 'name')
const currentDirection = ref(props.filters?.direction || 'asc')
const currentPage = ref(1)

// Filtrer les départements pour l'autocomplétion (min 2 caractères)
const filteredDepartments = computed(() => {
    if (departmentSearch.value.length < 2) return []
    return departments.value.filter(dept =>
        dept.name.toLowerCase().includes(departmentSearch.value.toLowerCase())
    )
})

const openModal = (city = null) => {
    selectedCity.value = city
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedCity.value = null
}

const handleSaved = () => {
    closeModal()
    fetchCities()
}

const getCurrentFilters = () => ({
    search: search.value,
    department_search: departmentSearch.value,
    department_id: selectedDepartmentId.value,
    population_operator: populationOperator.value,
    population_value: populationValue.value,
    sort: currentSort.value,
    direction: currentDirection.value,
    per_page: perPage.value,
    page: currentPage.value
})

const fetchCities = async () => {
    try {
        loading.value = true
        const params = getCurrentFilters()
        const data = await api.get('/api/cities', params)
        // S'assurer que c'est un objet avec data et links (pagination Laravel)
        cities.value = data || { data: [], links: [] }
    } catch (error) {
        console.error('Error fetching cities:', error)
        cities.value = { data: [], links: [] }
    } finally {
        loading.value = false
    }
}

const fetchDepartments = async () => {
    try {
        const data = await api.get('/api/departments')
        // S'assurer que c'est un array
        departments.value = Array.isArray(data) ? data : []
    } catch (error) {
        console.error('Error fetching departments:', error)
        departments.value = []
    }
}

const applyFilters = (overrides = {}) => {
    // Reset à la page 1 quand on applique des filtres (sauf si on change spécifiquement de page)
    if (!overrides.hasOwnProperty('page')) {
        currentPage.value = 1
    }
    fetchCities()
}

const handleSearch = () => {
    applyFilters()
}

const handleSort = (field) => {
    let direction = 'asc'

    if (currentSort.value === field) {
        direction = currentDirection.value === 'asc' ? 'desc' : 'asc'
    }

    currentSort.value = field
    currentDirection.value = direction
    applyFilters()
}

const handleDepartmentSearch = () => {
    // Si on avait un département sélectionné et que le texte a changé, on le désélectionne
    if (selectedDepartmentId.value) {
        const selectedDept = departments.value.find(d => d.id === selectedDepartmentId.value)
        if (selectedDept && departmentSearch.value !== selectedDept.name) {
            selectedDepartmentId.value = ''
            // Réactive l'autocomplétion si on a au moins 2 caractères
            if (departmentSearch.value.length >= 2) {
                showSuggestions.value = true
            }
        }
    }

    // Recherche LIKE si pas de département spécifique sélectionné
    if (!selectedDepartmentId.value) {
        applyFilters()
    }
}

const selectDepartment = (dept) => {
    departmentSearch.value = dept.name
    selectedDepartmentId.value = dept.id
    showSuggestions.value = false

    applyFilters({
        department_id: dept.id,
        department_search: dept.name
    })
}

const hideSuggestions = () => {
    // Délai pour permettre le clic sur les suggestions
    setTimeout(() => {
        showSuggestions.value = false
    }, 200)
}

const resetSort = () => {
    search.value = ''
    departmentSearch.value = ''
    selectedDepartmentId.value = ''
    populationOperator.value = ''
    populationValue.value = ''
    perPage.value = 10

    fetchCities()
}

const handlePerPageChange = () => {
    applyFilters()
}

const handlePageChange = (page) => {
    currentPage.value = page
    applyFilters({ page })
}

const handlePopulationFilter = () => {
    if ((populationOperator.value && populationValue.value) || (!populationOperator.value && !populationValue.value)) {
        applyFilters()
    }
}

const confirmDelete = async (city) => {
    const result = await Swal.fire({
        title: 'Êtes-vous sûr?',
        text: `Voulez-vous vraiment supprimer la ville "${city.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    })

    if (result.isConfirmed) {
        try {
            await api.delete(`/api/cities/${city.id}`)
            Swal.fire('Supprimé!', 'La ville a été supprimée.', 'success')
            fetchCities()
        } catch (error) {
            Swal.fire('Erreur!', 'Une erreur est survenue lors de la suppression.', 'error')
        }
    }
}

// Fetch initial data
onMounted(() => {
    fetchCities()
    fetchDepartments()
})
</script>
