<template>
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    {{ department ? 'Modifier le département' : 'Ajouter un département' }}
                </h3>
                
                <form @submit.prevent="submit">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom
                        </label>
                        <input 
                            v-model="form.name" 
                            type="text" 
                            id="name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                        />
                        <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code
                        </label>
                        <input 
                            v-model="form.code" 
                            type="text" 
                            id="code"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            required
                        />
                        <div v-if="form.errors.code" class="text-red-600 text-sm mt-1">
                            {{ form.errors.code }}
                        </div>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <button 
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Annuler
                        </button>
                        <button 
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50"
                        >
                            {{ form.processing ? 'En cours...' : (department ? 'Modifier' : 'Ajouter') }}
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
    department: Object
})

const emit = defineEmits(['close', 'saved'])

const form = useForm({
    name: '',
    code: ''
})

watch(() => props.department, (newDepartment) => {
    if (newDepartment) {
        form.name = newDepartment.name
        form.code = newDepartment.code
    } else {
        form.reset()
    }
}, { immediate: true })

const submit = () => {
    if (props.department) {
        form.put(`/departments/${props.department.id}`, {
            onSuccess: () => emit('saved'),
            preserveScroll: true
        })
    } else {
        form.post('/departments', {
            onSuccess: () => emit('saved'),
            preserveScroll: true
        })
    }
}
</script>