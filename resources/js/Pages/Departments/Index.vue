<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Gestion des Départements</h1>
                <button 
                    @click="openModal()"
                    class="bg-blue-500 hover:bg-blue-700 dark:bg-blue-600 dark:hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    Ajouter un département
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Nom
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Code
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        <tr v-for="department in departments.data" :key="department.id">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ department.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ department.code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button 
                                    @click="openModal(department)"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3"
                                >
                                    Modifier
                                </button>
                                <button 
                                    @click="confirmDelete(department)"
                                    class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                >
                                    Supprimer
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 flex justify-center" v-if="departments.links && departments.links.length > 3">
                <nav class="flex items-center space-x-2">
                    <template v-for="link in departments.links" :key="link.label">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'px-3 py-2 text-sm rounded-md',
                                link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                            ]"
                            v-html="link.label"
                            :only="['departments']"
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
            </div>
        </div>

        <!-- Modal -->
        <DepartmentForm 
            v-if="showModal"
            :department="selectedDepartment"
            @close="closeModal"
            @saved="handleSaved"
        />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import DepartmentForm from './DepartmentForm.vue'
import Swal from 'sweetalert2'

const props = defineProps({
    departments: Object
})

const showModal = ref(false)
const selectedDepartment = ref(null)

const openModal = (department = null) => {
    selectedDepartment.value = department
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedDepartment.value = null
}

const handleSaved = () => {
    closeModal()
    router.reload({ only: ['departments'] })
}

const confirmDelete = (department) => {
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: `Voulez-vous vraiment supprimer le département "${department.name}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/departments/${department.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        'Supprimé!',
                        'Le département a été supprimé.',
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