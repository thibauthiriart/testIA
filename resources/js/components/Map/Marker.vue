<template>
  <div 
    class="marker-pin rounded-full border-2 border-white shadow-lg"
    :style="{
      backgroundColor: markerColor,
      width: `${iconSize}px`,
      height: `${iconSize}px`
    }"
  ></div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  population: {
    type: Number,
    required: true
  }
})

// Get icon size based on population
const iconSize = computed(() => {
  if (props.population > 500000) return 16
  if (props.population > 100000) return 14
  if (props.population > 50000) return 12
  if (props.population > 20000) return 10
  return 8
})

// Get marker color based on population
const markerColor = computed(() => {
  if (props.population > 500000) return '#dc2626' // red-600
  if (props.population > 100000) return '#ea580c' // orange-600
  if (props.population > 50000) return '#d97706' // amber-600
  if (props.population > 20000) return '#16a34a' // green-600
  return '#2563eb' // blue-600
})
</script>