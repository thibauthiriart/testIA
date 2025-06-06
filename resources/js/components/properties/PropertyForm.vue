<template>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            {{ property ? 'Modifier la propriété' : 'Ajouter une propriété' }}
                        </h3>
                        <button
                            @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            <span class="sr-only">Fermer</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <!-- Titre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Titre *
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ex: Appartement T3 lumineux centre ville"
                            />
                            <span v-if="errors.title" class="text-red-500 text-sm">{{ errors.title }}</span>
                        </div>

                        <!-- URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                URL
                            </label>
                            <input
                                v-model="form.url"
                                type="url"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="https://..."
                            />
                            <span v-if="errors.url" class="text-red-500 text-sm">{{ errors.url }}</span>
                        </div>

                        <!-- Prix et Type de transaction -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Prix (€) *
                                </label>
                                <input
                                    v-model.number="form.price"
                                    type="number"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <span v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type de transaction *
                                </label>
                                <select
                                    v-model="form.transaction_type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">Sélectionner</option>
                                    <option value="sale">Vente</option>
                                    <option value="rent">Location</option>
                                </select>
                                <span v-if="errors.transaction_type" class="text-red-500 text-sm">{{ errors.transaction_type }}</span>
                            </div>
                        </div>

                        <!-- Type de bien et Ville -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Type de bien *
                                </label>
                                <select
                                    v-model="form.property_type"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">Sélectionner</option>
                                    <option value="apartment">Appartement</option>
                                    <option value="house">Maison</option>
                                    <option value="villa">Villa</option>
                                    <option value="studio">Studio</option>
                                    <option value="land">Terrain</option>
                                    <option value="parking">Parking</option>
                                    <option value="other">Autre</option>
                                </select>
                                <span v-if="errors.property_type" class="text-red-500 text-sm">{{ errors.property_type }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Ville *
                                </label>
                                <select
                                    v-model="form.city_id"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                    <option value="">Sélectionner une ville</option>
                                    <option v-for="city in cities" :key="city.id" :value="city.id">
                                        {{ city.name }}
                                    </option>
                                </select>
                                <span v-if="errors.city_id" class="text-red-500 text-sm">{{ errors.city_id }}</span>
                            </div>
                        </div>

                        <!-- Surface et Pièces -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Surface (m²)
                                </label>
                                <input
                                    v-model.number="form.surface"
                                    type="number"
                                    min="0"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <span v-if="errors.surface" class="text-red-500 text-sm">{{ errors.surface }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Nombre de pièces
                                </label>
                                <input
                                    v-model.number="form.rooms"
                                    type="number"
                                    min="1"
                                    max="20"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                />
                                <span v-if="errors.rooms" class="text-red-500 text-sm">{{ errors.rooms }}</span>
                            </div>
                        </div>

                        <!-- Source -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Source
                            </label>
                            <input
                                v-model="form.source"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Ex: SeLoger, LeBonCoin, Manuel..."
                            />
                            <span v-if="errors.source" class="text-red-500 text-sm">{{ errors.source }}</span>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Description de la propriété..."
                            ></textarea>
                            <span v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</span>
                        </div>

                        <!-- Images -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Images (URLs séparées par des virgules)
                            </label>
                            <textarea
                                v-model="form.images_text"
                                rows="3"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 bg-white text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="https://image1.jpg, https://image2.jpg, ..."
                            ></textarea>
                            <p class="text-xs text-gray-500 mt-1">Séparez les URLs d'images par des virgules</p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button
                                type="button"
                                @click="closeModal"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600"
                            >
                                Annuler
                            </button>
                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 dark:bg-indigo-700 border border-transparent rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-800 disabled:opacity-50"
                            >
                                <span v-if="!loading">{{ property ? 'Modifier' : 'Ajouter' }}</span>
                                <span v-else>Enregistrement...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    property: {
        type: Object,
        default: null
    },
    cities: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['close', 'saved'])

const loading = ref(false)
const errors = ref({})

const form = reactive({
    title: '',
    url: '',
    price: '',
    transaction_type: '',
    property_type: '',
    city_id: '',
    surface: '',
    rooms: '',
    source: 'Manuel',
    description: '',
    images_text: ''
})

// Watch for property changes to populate form
watch(() => props.property, (newProperty) => {
    if (newProperty) {
        form.title = newProperty.title || ''
        // Don't show URL if it's "annonce inconnue" or "#"
        form.url = (newProperty.url === 'annonce inconnue' || newProperty.url === '#') ? '' : (newProperty.url || '')
        form.price = newProperty.price || ''
        form.transaction_type = newProperty.transaction_type || ''
        form.property_type = newProperty.property_type || ''
        form.city_id = newProperty.city_id || ''
        form.surface = newProperty.surface || ''
        form.rooms = newProperty.rooms || ''
        form.source = newProperty.source || 'Manuel'
        form.description = newProperty.description || ''
        form.images_text = newProperty.images ? newProperty.images.join(', ') : ''
    } else {
        // Reset form for new property
        Object.keys(form).forEach(key => {
            form[key] = key === 'source' ? 'Manuel' : ''
        })
    }
}, { immediate: true })

const closeModal = () => {
    emit('close')
}

const submitForm = () => {
    loading.value = true
    errors.value = {}

    // Prepare form data
    const formData = { ...form }
    
    // Convert images_text to array
    if (formData.images_text) {
        formData.images = formData.images_text.split(',').map(url => url.trim()).filter(url => url)
    }
    delete formData.images_text

    // Remove empty fields
    Object.keys(formData).forEach(key => {
        if (formData[key] === '' || formData[key] === null) {
            delete formData[key]
        }
    })

    const url = props.property 
        ? `/properties/${props.property.id}` 
        : '/properties'
    
    const method = props.property ? 'put' : 'post'

    router[method](url, formData, {
        onSuccess: () => {
            emit('saved')
        },
        onError: (responseErrors) => {
            errors.value = responseErrors
        },
        onFinish: () => {
            loading.value = false
        }
    })
}
</script>