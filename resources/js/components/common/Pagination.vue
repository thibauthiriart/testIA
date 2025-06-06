<template>
    <nav class="flex items-center space-x-2" v-if="links && links.length > 3">
        <template v-for="link in links" :key="link.label">
            <button
                v-if="link.url && !link.active"
                @click="handlePageChange(link.url)"
                :class="[
                    'px-3 py-2 text-sm rounded-md',
                    'bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors'
                ]"
                v-html="link.label"
            />
            <span
                v-else-if="link.active"
                :class="[
                    'px-3 py-2 text-sm rounded-md',
                    'bg-blue-500 text-white'
                ]"
                v-html="link.label"
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
const props = defineProps({
    links: Array,
    only: {
        type: Array,
        default: () => []
    }
})

const emit = defineEmits(['page-change'])

const handlePageChange = (url) => {
    // Extraire le numéro de page de l'URL
    const urlObj = new URL(url)
    const page = urlObj.searchParams.get('page')
    if (page) {
        emit('page-change', parseInt(page))
    }
}
</script>