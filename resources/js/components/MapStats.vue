<template>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg transition-colors duration-300">
      <div class="text-lg font-semibold text-blue-900 dark:text-blue-100">
        {{ citiesCount }}
      </div>
      <div class="text-sm text-blue-700 dark:text-blue-300">
        Villes affichées
      </div>
    </div>
    
    <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg transition-colors duration-300">
      <div class="text-lg font-semibold text-green-900 dark:text-green-100">
        {{ formattedPopulation }}
      </div>
      <div class="text-sm text-green-700 dark:text-green-300">
        Population totale
      </div>
    </div>
    
    <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg transition-colors duration-300">
      <div class="text-lg font-semibold text-purple-900 dark:text-purple-100">
        {{ departmentsCount }}
      </div>
      <div class="text-sm text-purple-700 dark:text-purple-300">
        Départements
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  citiesCount: {
    type: Number,
    default: 0
  },
  totalPopulation: {
    type: Number,
    default: 0
  },
  departmentsCount: {
    type: Number,
    default: 0
  }
})

// Format population with French locale
const formattedPopulation = computed(() => {
  return props.totalPopulation.toLocaleString('fr-FR')
})
</script>