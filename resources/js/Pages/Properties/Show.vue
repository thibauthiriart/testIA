<template>
    <AppLayout title="Détail de la propriété">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6">
                    <button
                        @click="$inertia.visit('/properties')"
                        class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white"
                    >
                        ← Retour aux propriétés
                    </button>
                </div>

                <!-- Property Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <!-- Images -->
                    <div v-if="property.images && property.images.length > 0" class="h-96 bg-gray-200">
                        <img
                            :src="property.images[0]"
                            :alt="property.title"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div v-else class="h-96 bg-gray-300 flex items-center justify-center">
                        <span class="text-gray-500">Aucune image disponible</span>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ property.title }}
                        </h1>

                        <!-- Price and location -->
                        <div class="mb-6">
                            <div class="text-3xl font-bold text-blue-600 mb-2">
                                {{ formatPrice(property.price) }}
                                <span v-if="property.transaction_type === 'rent'" class="text-lg">/mois</span>
                            </div>
                            <div v-if="property.city_model" class="text-lg text-gray-600 dark:text-gray-400">
                                📍 {{ property.city_model.name }} ({{ property.city_model.postal_code }})
                            </div>
                        </div>

                        <!-- Details grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div v-if="property.surface" class="flex items-center">
                                <span class="text-gray-500 mr-2">📐</span>
                                <span class="font-medium">Surface :</span>
                                <span class="ml-2">{{ property.surface }} m²</span>
                            </div>
                            <div v-if="property.rooms" class="flex items-center">
                                <span class="text-gray-500 mr-2">🏠</span>
                                <span class="font-medium">Pièces :</span>
                                <span class="ml-2">{{ property.rooms }}</span>
                            </div>
                            <div v-if="property.bedrooms" class="flex items-center">
                                <span class="text-gray-500 mr-2">🛏️</span>
                                <span class="font-medium">Chambres :</span>
                                <span class="ml-2">{{ property.bedrooms }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-gray-500 mr-2">🏢</span>
                                <span class="font-medium">Type :</span>
                                <span class="ml-2">{{ propertyTypeLabel }}</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="property.description" class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h3>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                {{ property.description }}
                            </p>
                        </div>

                        <!-- Actions -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">
                                    Annonce publiée le {{ formatDate(property.scraped_at) }}
                                </span>
                                <a
                                    v-if="property.url"
                                    :href="property.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors"
                                >
                                    Voir sur {{ property.source }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    property: {
        type: Object,
        required: true
    }
})

const formatPrice = (price) => {
    if (!price) return 'Prix non renseigné'
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
    }).format(price)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    })
}

const propertyTypeLabel = computed(() => {
    const types = {
        'apartment': 'Appartement',
        'house': 'Maison',
        'villa': 'Villa',
        'studio': 'Studio',
        'land': 'Terrain',
        'parking': 'Parking',
        'commercial': 'Local commercial',
        'other': 'Autre'
    }
    return types[props.property.property_type] || 'Bien immobilier'
})
</script>