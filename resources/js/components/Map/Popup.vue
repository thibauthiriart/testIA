<template>
  <div class="p-3 min-w-[200px]">
    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2">{{ city.name }}</h3>
    <div class="space-y-1 text-sm">
      <div class="text-gray-700 dark:text-gray-300">
        <span class="font-medium">Population:</span> {{ city.population.toLocaleString() }}
      </div>
      <div class="text-gray-700 dark:text-gray-300">
        <span class="font-medium">Code postal:</span> {{ city.postal_code }}
      </div>
      <div class="text-gray-700 dark:text-gray-300">
        <span class="font-medium">Département:</span> {{ city.department.name }} ({{ city.department.code }})
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  city: {
    type: Object,
    required: true
  }
})
</script>