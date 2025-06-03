<template>
    <button
        @click="$emit('sort')"
        class="flex items-center space-x-1 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none"
    >
        <span>{{ label }}</span>
        <svg v-if="isActive" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path v-if="direction === 'asc'" fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
            <path v-else fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>
</template>

<script setup>
defineProps({
    label: String,
    isActive: Boolean,
    direction: {
        type: String,
        default: 'asc'
    }
})

defineEmits(['sort'])
</script>