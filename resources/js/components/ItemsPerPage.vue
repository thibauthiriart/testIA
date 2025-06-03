<template>
    <div class="flex items-center space-x-2">
        <label :for="id" class="text-sm text-gray-700 dark:text-gray-300">{{ label }}</label>
        <select
            :id="id"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded px-3 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400"
        >
            <option v-for="option in options" :key="option" :value="option">
                {{ option }}
            </option>
        </select>
        <span class="text-sm text-gray-700 dark:text-gray-300">{{ suffix }}</span>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    modelValue: [String, Number],
    label: {
        type: String,
        default: 'Afficher'
    },
    suffix: {
        type: String,
        default: 'éléments'
    },
    options: {
        type: Array,
        default: () => [5, 10, 25, 50, 100]
    }
})

defineEmits(['update:modelValue'])

const id = computed(() => `per-page-${Math.random().toString(36).substr(2, 9)}`)
</script>