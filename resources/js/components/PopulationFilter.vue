<template>
  <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg transition-colors duration-300">
    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
      Filtrer par nombre d'habitants
    </h4>
    
    <!-- Population Range Buttons -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mb-4">
      <button
        v-for="range in populationRanges"
        :key="range.id"
        @click="setPopulationFilter(range)"
        :class="[
          'px-4 py-3 rounded-lg border text-sm font-medium transition-all duration-200',
          selectedRange?.id === range.id
            ? 'bg-blue-500 text-white border-blue-500 shadow-md transform scale-105'
            : 'bg-white dark:bg-gray-600 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-500 hover:bg-blue-50 dark:hover:bg-gray-500 hover:border-blue-300 dark:hover:border-blue-400'
        ]"
      >
        <div class="font-semibold">{{ range.label }}</div>
        <div class="text-xs opacity-75">{{ range.description }}</div>
      </button>
    </div>

    <!-- Custom Range Slider -->
    <div class="mt-6">
      <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
          Plage personnalisée
        </label>
        <button
          @click="resetFilter"
          class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
        >
          Réinitialiser
        </button>
      </div>
      <div class="flex items-center space-x-4">
        <div class="flex-1">
          <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Minimum</label>
          <input
            v-model.number="customMin"
            @input="applyCustomFilter"
            type="number"
            min="0"
            step="1000"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
            placeholder="0"
          />
        </div>
        <div class="text-gray-400">-</div>
        <div class="flex-1">
          <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Maximum</label>
          <input
            v-model.number="customMax"
            @input="applyCustomFilter"
            type="number"
            min="0"
            step="1000"
            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
            placeholder="∞"
          />
        </div>
      </div>
    </div>

    <!-- Filter Info -->
    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
      {{ filteredCount }} ville(s) affichée(s)
      {{ selectedRange ? `(${selectedRange.label})` : customMin || customMax ? `(${customMin || 0} - ${customMax || '∞'} habitants)` : '' }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  filteredCount: {
    type: Number,
    default: 0
  },
  initialRange: {
    type: String,
    default: 'all'
  }
})

const emit = defineEmits(['filter-changed'])

// Population filter states
const selectedRange = ref(null)
const customMin = ref(null)
const customMax = ref(null)

// Population ranges for filtering
const populationRanges = [
  { id: 'all', label: 'Toutes', description: 'Toutes les villes', min: 0, max: Infinity },
  { id: 'small', label: '< 10k', description: 'Petites villes', min: 0, max: 10000 },
  { id: 'medium', label: '10k - 50k', description: 'Villes moyennes', min: 10000, max: 50000 },
  { id: 'large', label: '50k - 100k', description: 'Grandes villes', min: 50000, max: 100000 },
  { id: 'metro', label: '100k - 500k', description: 'Métropoles', min: 100000, max: 500000 },
  { id: 'mega', label: '> 500k', description: 'Mégapoles', min: 500000, max: Infinity }
]

// Initialize with default range
selectedRange.value = populationRanges.find(r => r.id === props.initialRange) || populationRanges[0]

// Current filter computed property
const currentFilter = computed(() => {
  let min = 0
  let max = Infinity
  
  if (selectedRange.value) {
    min = selectedRange.value.min
    max = selectedRange.value.max
  } else if (customMin.value !== null || customMax.value !== null) {
    min = customMin.value || 0
    max = customMax.value || Infinity
  }
  
  return { min, max }
})

// Utility function to clear custom inputs and emit change
const clearCustomInputsAndEmit = (newRange = null) => {
  selectedRange.value = newRange
  customMin.value = null
  customMax.value = null
  emitFilterChange()
}

// Filter functions
const setPopulationFilter = (range) => {
  clearCustomInputsAndEmit(range)
}

const applyCustomFilter = () => {
  selectedRange.value = null
  emitFilterChange()
}

const resetFilter = () => {
  clearCustomInputsAndEmit(populationRanges[0]) // 'Toutes'
}

const emitFilterChange = () => {
  emit('filter-changed', currentFilter.value)
}

// Emit initial filter on mount
emitFilterChange()
</script>