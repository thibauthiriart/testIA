<template>
    <div class="p-3 min-w-[200px]">
        <h3 class="font-bold text-sm mb-2">{{ property.title }}</h3>

        <div class="space-y-1">
            <p class="text-sm text-gray-600">
                {{ formatPrice(property.price) }}
                <span v-if="property.transaction_type === 'rent'" class="text-xs">/mois</span>
            </p>

            <p class="text-xs text-gray-500">
                {{ property.city_model?.name || 'Ville inconnue' }}
                <span v-if="property.city_model?.postal_code">({{ property.city_model.postal_code }})</span>
            </p>

            <div v-if="property.surface" class="text-xs text-gray-500">
                {{ property.surface }} m²
            </div>

            <div v-if="property.rooms" class="text-xs text-gray-500">
                {{ property.rooms }} pièce{{ property.rooms > 1 ? 's' : '' }}
            </div>

            <div v-if="property.property_type" class="text-xs text-gray-500">
                {{ propertyTypeLabel }}
            </div>
        </div>

        <div v-if="property.url" class="mt-3 pt-3 border-t border-gray-200">
            <a
                :href="property.url"
                target="_blank"
                rel="noopener noreferrer"
                class="text-blue-600 text-xs hover:text-blue-800 hover:underline font-medium"
            >
                Voir l'annonce →
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

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
        maximumFractionDigits: 0
    }).format(price)
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
