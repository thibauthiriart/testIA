<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Gestion des Villes</h1>
                <button 
                    @click="openModal()"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Ajouter une ville
                </button>
            </div>

            <DataTable>
                <template #headers>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex flex-col">
                            <SortableHeader
                                label="Nom"
                                :is-active="filters.sort === 'name'"
                                :direction="filters.direction"
                                @sort="() => tableFilters.handleSort('name', filters.sort, filters.direction)"
                            />
                            <SearchInput
                                v-model="search"
                                class="mt-2"
                                @input="handleSearch"
                            />
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex flex-col">
                            <SortableHeader
                                label="Population"
                                :is-active="filters.sort === 'population'"
                                :direction="filters.direction"
                                @sort="() => tableFilters.handleSort('population', filters.sort, filters.direction)"
                            />
                            <div class="flex items-center mt-2 space-x-1">
                                <select
                                    v-model="populationOperator"
                                    class="px-1 py-1 text-sm font-normal normal-case border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                    class="w-20 px-2 py-1 text-sm font-normal normal-case border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    @input="handlePopulationFilter"
                                    min="0"
                                />
                            </div>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex flex-col">
                            <SortableHeader
                                label="Département"
                                :is-active="filters.sort === 'department'"
                                :direction="filters.direction"
                                @sort="() => tableFilters.handleSort('department', filters.sort, filters.direction)"
                            />
                            <div class="relative mt-2">
                                <SearchInput
                                    v-model="departmentSearch"
                                    @input="handleDepartmentSearch"
                                    @focus="showSuggestions = true"
                                    @blur="hideSuggestions"
                                />
                                <div v-if="showSuggestions && filteredDepartments.length > 0" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                    <button
                                        v-for="dept in filteredDepartments"
                                        :key="dept.id"
                                        @mousedown.prevent="selectDepartment(dept)"
                                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                                    >
                                        {{ dept.nom }} ({{ dept.code }})
                                    </button>
                                </div>
                            </div>
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <button
                            @click="tableFilters.resetFilters"
                            class="flex items-center space-x-1 text-red-600 hover:text-red-800 focus:outline-none"
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ city.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ city.population.toLocaleString('fr-FR') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ city.department.name }} ({{ city.department.code }})
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button 
                                @click="openModal(city)"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                            >
                                Modifier
                            </button>
                            <button 
                                @click="confirmDelete(city)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Supprimer
                            </button>
                        </td>
                        <td class="px-6 py-4"></td>
                    </tr>
                </template>
            </DataTable>

            <div class="mt-6 flex justify-between items-center">
                <ItemsPerPage
                    v-model="perPage"
                    @update:modelValue="handlePerPageChange"
                />
                
                <Pagination
                    :links="cities.links"
                    :only="['cities']"
                />
                
                <div v-if="!(cities.links && cities.links.length > 3)" class="w-32"></div>
            </div>
        </div>

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
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CityForm from './CityForm.vue'
import DataTable from '@/components/DataTable.vue'
import SortableHeader from '@/components/SortableHeader.vue'
import SearchInput from '@/components/SearchInput.vue'
import Pagination from '@/components/Pagination.vue'
import ItemsPerPage from '@/components/ItemsPerPage.vue'
import { useTableFilters } from '@/composables/useTableFilters'
import Swal from 'sweetalert2'

const props = defineProps({
    cities: Object,
    departments: Array,
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

const getCurrentFilters = () => ({
    search: search.value,
    department_search: departmentSearch.value,
    department_id: selectedDepartmentId.value,
    population_operator: populationOperator.value,
    population_value: populationValue.value,
    per_page: perPage.value
})

const tableFilters = useTableFilters('/cities', {
    ...getCurrentFilters(),
    sort: props.filters?.sort,
    direction: props.filters?.direction
})

const applyCurrentFilters = (overrides = {}) => {
    tableFilters.applyFilters({
        ...getCurrentFilters(),
        ...overrides
    })
}

const filteredDepartments = computed(() => {
    if (departmentSearch.value.length < 2) return []
    return props.departments.filter(dept => 
        dept.nom.toLowerCase().includes(departmentSearch.value.toLowerCase())
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
    router.reload({ only: ['cities'] })
}

const handleSearch = () => {
    applyCurrentFilters()
}

const handleDepartmentSearch = () => {
    if (selectedDepartmentId.value) {
        const selectedDept = props.departments.find(d => d.id === selectedDepartmentId.value)
        if (selectedDept && departmentSearch.value !== selectedDept.nom) {
            selectedDepartmentId.value = ''
            if (departmentSearch.value.length >= 2) {
                showSuggestions.value = true
            }
        }
    }
    
    if (!selectedDepartmentId.value) {
        applyCurrentFilters()
    }
}

const selectDepartment = (dept) => {
    departmentSearch.value = dept.nom
    selectedDepartmentId.value = dept.id
    showSuggestions.value = false
    
    applyCurrentFilters({
        department_id: dept.id,
        department_search: dept.nom
    })
}

const hideSuggestions = () => {
    setTimeout(() => {
        showSuggestions.value = false
    }, 200)
}

const handlePerPageChange = () => {
    applyCurrentFilters()
}

const handlePopulationFilter = () => {
    if ((populationOperator.value && populationValue.value) || (!populationOperator.value && !populationValue.value)) {
        applyCurrentFilters()
    }
}

const confirmDelete = (city) => {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: `Voulez-vous vraiment supprimer la ville "${city.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/cities/${city.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        'Supprimé!',
                        'La ville a été supprimée.',
                        'success'
                    )
                },
                onError: () => {
                    Swal.fire(
                        'Erreur!',
                        'Une erreur est survenue lors de la suppression.',
                        'error'
                    )
                }
            })
        }
    })
}
</script>