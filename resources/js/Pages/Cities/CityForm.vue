<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border border-gray-300 dark:border-gray-600 w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
                    {{ city ? 'Modifier la ville' : 'Ajouter une ville' }}
                </h3>
                
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Nom
                        </label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            id="name"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                            required
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Code postal
                        </label>
                        <input 
                            v-model="form.postal_code" 
                            type="text" 
                            id="postal_code"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                            required
                            pattern="[0-9]{5}"
                            maxlength="5"
                            placeholder="75001"
                        />
                        <div v-if="form.errors.postal_code" class="text-red-600 text-sm mt-1">
                            {{ form.errors.postal_code }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="population" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Population
                        </label>
                        <input 
                            v-model="form.population" 
                            type="number" 
                            id="population"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                            min="0"
                        />
                        <div v-if="form.errors.population" class="text-red-600 text-sm mt-1">
                            {{ form.errors.population }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Département
                        </label>
                        <select 
                            v-model="form.department_id" 
                            id="department_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                            required
                        >
                            <option value="">Sélectionner un département</option>
                            <option 
                                v-for="department in departments" 
                                :key="department.id" 
                                :value="department.id"
                            >
                                {{ department.name }} ({{ department.code }})
                            </option>
                        </select>
                        <div v-if="form.errors.department_id" class="text-red-600 text-sm mt-1">
                            {{ form.errors.department_id }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button 
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50"
                        >
                            {{ form.processing ? 'En cours...' : (city ? 'Modifier' : 'Ajouter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'

const props = defineProps({
    city: Object,
    departments: Array
})

const emit = defineEmits(['close', 'saved'])

const form = useForm({
    name: '',
    postal_code: '',
    population: '',
    department_id: ''
})

watch(() => props.city, (newCity) => {
    if (newCity) {
        form.name = newCity.name
        form.postal_code = newCity.postal_code
        form.population = newCity.population
        form.department_id = newCity.department_id
    } else {
        form.reset()
    }
}, { immediate: true })

const submit = () => {
    if (props.city) {
        form.put(`/cities/${props.city.id}`, {
            onSuccess: () => emit('saved'),
            preserveScroll: true
        })
    } else {
        form.post('/cities', {
            onSuccess: () => emit('saved'),
            preserveScroll: true
        })
    }
}
</script>