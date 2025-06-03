<template>
    <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        type="text"
        :placeholder="placeholder"
        class="px-2 py-1 text-sm font-normal normal-case border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400"
        v-bind="$attrs"
    />
</template>

<script setup>
defineProps({
    modelValue: String,
    placeholder: {
        type: String,
        default: 'Rechercher...'
    }
})

defineEmits(['update:modelValue'])
</script>