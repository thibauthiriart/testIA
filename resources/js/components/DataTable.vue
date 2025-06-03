<template>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <slot name="headers" />
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                <slot name="body" />
            </tbody>
        </table>
    </div>
</template>