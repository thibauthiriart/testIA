<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
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
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-lg">{{ property.title }}</h3>
                
                <!-- Menu dropdown -->
                <div class="relative">
                    <button
                        @click="toggleDropdown"
                        class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        title="Actions"
                    >
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </button>
                    
                    <!-- Dropdown menu -->
                    <div
                        v-if="showDropdown"
                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-600"
                        @click.stop
                    >
                        <div class="py-1">
                            <button
                                @click="editProperty"
                                class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Modifier
                            </button>
                            <button
                                @click="deleteProperty"
                                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-2xl font-bold text-blue-600 mb-2">
                {{ formatPrice(property.price) }}
                <span v-if="property.transaction_type === 'rent'" class="text-sm">/mois</span>
            </div>

            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                <div v-if="property.city_model">
                    📍 {{ property.city_model.name }} ({{ property.city_model.postal_code }})
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
                    v-if="property.url && property.url !== 'annonce inconnue'"
                    :href="property.url"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-blue-600 hover:text-blue-800 text-sm"
                >
                    Voir sur {{ property.source }}
                </a>
                <span v-else class="text-xs text-gray-400">
                    Ajouté manuellement
                </span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Swal from 'sweetalert2'

const props = defineProps({
    property: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['edit', 'delete'])

const showDropdown = ref(false)

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
}

const editProperty = () => {
    showDropdown.value = false
    emit('edit', props.property)
}

const deleteProperty = () => {
    showDropdown.value = false
    
    Swal.fire({
        title: 'Êtes-vous sûr?',
        text: `Voulez-vous vraiment supprimer la propriété "${props.property.title}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer!',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/properties/${props.property.id}`, {
                onSuccess: () => {
                    Swal.fire(
                        'Supprimé!',
                        'La propriété a été supprimée.',
                        'success'
                    )
                    // Force a complete reload to refresh the properties list
                    window.location.reload()
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

const handleClickOutside = (event) => {
    if (!event.target.closest('.relative')) {
        showDropdown.value = false
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

const formatPrice = (price) => {
    if (!price) return 'Prix non renseigné';
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
    }).format(price);
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}
</script>