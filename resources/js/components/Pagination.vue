<template>
    <nav class="flex items-center space-x-2" v-if="links && links.length > 3">
        <template v-for="link in links" :key="link.label">
            <Link
                v-if="link.url"
                :href="link.url"
                :class="[
                    'px-3 py-2 text-sm rounded-md',
                    link.active ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500'
                ]"
                v-html="link.label"
                :only="only"
            />
            <span
                v-else
                :class="[
                    'px-3 py-2 text-sm rounded-md',
                    'bg-gray-200 dark:bg-gray-600 text-gray-400 dark:text-gray-500 cursor-not-allowed opacity-50'
                ]"
                v-html="link.label"
            />
        </template>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Array,
    only: {
        type: Array,
        default: () => []
    }
})
</script>