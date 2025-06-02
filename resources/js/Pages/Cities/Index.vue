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

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col">
                                    <button
                                        @click="handleSort('name')"
                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                    >
                                        <span>Nom</span>
                                        <svg v-if="filters.sort === 'name'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="filters.direction === 'asc'" fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <input
                                        v-model="search"
                                        type="text"
                                        placeholder="Rechercher..."
                                        class="mt-2 px-2 py-1 text-sm font-normal normal-case border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                        @input="handleSearch"
                                    />
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col">
                                    <button
                                        @click="handleSort('population')"
                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                    >
                                        <span>Population</span>
                                        <svg v-if="filters.sort === 'population'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="filters.direction === 'asc'" fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
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
                                    <button
                                        @click="handleSort('department')"
                                        class="flex items-center space-x-1 hover:text-gray-700 focus:outline-none"
                                    >
                                        <span>Département</span>
                                        <svg v-if="filters.sort === 'department'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path v-if="filters.direction === 'asc'" fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                            <path v-else fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="relative mt-2">
                                        <input
                                            v-model="departmentSearch"
                                            type="text"
                                            placeholder="Rechercher..."
                                            class="px-2 py-1 text-sm font-normal normal-case border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
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
                                    @click="resetSort"
                                    class="flex items-center space-x-1 text-red-600 hover:text-red-800 focus:outline-none"
                                    title="Réinitialiser le tri"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-xs">Réinitialiser</span>
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
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
                    </tbody>
                </table>
            </div>

            <!-- Pagination et Items par page -->
            <div class="mt-6 flex justify-between items-center">
                <!-- Sélecteur d'items par page -->
                <div class="flex items-center space-x-2">
                    <label for="per-page" class="text-sm text-gray-700">Afficher</label>
                    <select
                        id="per-page"
                        v-model="perPage"
                        @change="handlePerPageChange"
                        class="border border-gray-300 rounded px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <span class="text-sm text-gray-700">éléments</span>
                </div>
                
                <!-- Pagination -->
                <nav class="flex items-center space-x-2" v-if="cities.links && cities.links.length > 3">
                    <template v-for="link in cities.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                            v-html="link.label"
                            :only="['cities']"
                        />
                        <span
                            v-else
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                'bg-gray-200 text-gray-400 cursor-not-allowed opacity-50'
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </nav>
                
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
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import CityForm from './CityForm.vue'
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

// Filtrer les départements pour l'autocomplétion (min 2 caractères)
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
    router.get('/cities', { 
        search: search.value,
        department_search: departmentSearch.value,
        department_id: selectedDepartmentId.value,
        population_operator: populationOperator.value,
        population_value: populationValue.value,
        sort: props.filters?.sort || 'nom',
        direction: props.filters?.direction || 'asc',
        per_page: perPage.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['cities']
    })
}

const handleSort = (field) => {
    let direction = 'asc'
    
    // Si on clique sur la même colonne, on inverse la direction
    if (props.filters?.sort === field) {
        direction = props.filters.direction === 'asc' ? 'desc' : 'asc'
    }
    
    router.get('/cities', {
        search: search.value,
        department_search: departmentSearch.value,
        department_id: selectedDepartmentId.value,
        population_operator: populationOperator.value,
        population_value: populationValue.value,
        sort: field,
        direction: direction,
        per_page: perPage.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['cities', 'filters']
    })
}

const handleDepartmentSearch = () => {
    // Si on avait un département sélectionné et que le texte a changé, on le désélectionne
    if (selectedDepartmentId.value) {
        const selectedDept = props.departments.find(d => d.id === selectedDepartmentId.value)
        if (selectedDept && departmentSearch.value !== selectedDept.nom) {
            selectedDepartmentId.value = ''
            // Réactive l'autocomplétion si on a au moins 2 caractères
            if (departmentSearch.value.length >= 2) {
                showSuggestions.value = true
            }
        }
    }
    
    // Recherche LIKE si pas de département spécifique sélectionné
    if (!selectedDepartmentId.value) {
        router.get('/cities', {
            search: search.value,
            department_search: departmentSearch.value,
            population_operator: populationOperator.value,
            population_value: populationValue.value,
            sort: props.filters?.sort || 'nom',
            direction: props.filters?.direction || 'asc',
            per_page: perPage.value
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['cities', 'filters']
        })
    }
}

const selectDepartment = (dept) => {
    departmentSearch.value = dept.nom
    selectedDepartmentId.value = dept.id
    showSuggestions.value = false
    
    // Recherche par ID de département spécifique
    router.get('/cities', {
        search: search.value,
        department_id: dept.id,
        department_search: dept.nom,
        population_operator: populationOperator.value,
        population_value: populationValue.value,
        sort: props.filters?.sort || 'nom',
        direction: props.filters?.direction || 'asc',
        per_page: perPage.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['cities', 'filters']
    })
}

const hideSuggestions = () => {
    // Délai pour permettre le clic sur les suggestions
    setTimeout(() => {
        showSuggestions.value = false
    }, 200)
}

const resetSort = () => {
    // Vider tous les champs de recherche
    search.value = ''
    departmentSearch.value = ''
    selectedDepartmentId.value = ''
    populationOperator.value = ''
    populationValue.value = ''
    
    // Faire la requête sans aucun filtre
    router.get('/cities', {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['cities', 'filters']
    })
}

const handlePerPageChange = () => {
    router.get('/cities', {
        search: search.value,
        department_search: departmentSearch.value,
        department_id: selectedDepartmentId.value,
        population_operator: populationOperator.value,
        population_value: populationValue.value,
        sort: props.filters?.sort || 'nom',
        direction: props.filters?.direction || 'asc',
        per_page: perPage.value
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['cities', 'filters']
    })
}

const handlePopulationFilter = () => {
    // Ne faire la requête que si les deux valeurs sont présentes ou si les deux sont vides
    if ((populationOperator.value && populationValue.value) || (!populationOperator.value && !populationValue.value)) {
        router.get('/cities', {
            search: search.value,
            department_search: departmentSearch.value,
            department_id: selectedDepartmentId.value,
            population_operator: populationOperator.value,
            population_value: populationValue.value,
            sort: props.filters?.sort || 'nom',
            direction: props.filters?.direction || 'asc',
            per_page: perPage.value
        }, {
            preserveState: true,
            preserveScroll: true,
            only: ['cities', 'filters']
        })
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