<template>
    <AppLayout>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-3xl mx-auto">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mon compte</h1>
                    
                    <div v-if="$page.props.flash?.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                    </div>

                    <form @submit.prevent="updateAccount" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                                <input
                                    id="last_name"
                                    v-model="form.last_name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                                <div v-if="errors.last_name" class="text-red-600 text-sm mt-1">{{ errors.last_name }}</div>
                            </div>

                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                                <input
                                    id="first_name"
                                    v-model="form.first_name"
                                    type="text"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                                />
                                <div v-if="errors.first_name" class="text-red-600 text-sm mt-1">{{ errors.first_name }}</div>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Adresse email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                            />
                            <div v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</div>
                        </div>

                        <div class="flex justify-between items-center pt-4">
                            <div class="text-sm text-gray-600">
                                Membre depuis le {{ formatDate(user.created_at) }}
                            </div>
                            <button
                                type="submit"
                                :disabled="processing"
                                class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                            >
                                <span v-if="processing">Mise à jour...</span>
                                <span v-else>Mettre à jour</span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sécurité</h2>
                        <button
                            @click="showPasswordModal = true"
                            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Changer mon mot de passe
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal changement de mot de passe -->
        <div v-if="showPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center">
            <div class="bg-white p-5 rounded-lg shadow-xl max-w-md w-full">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Changer le mot de passe</h3>
                <form @submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Mot de passe actuel</label>
                        <input
                            id="current_password"
                            v-model="passwordForm.current_password"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                        <input
                            id="new_password"
                            v-model="passwordForm.password"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmer le nouveau mot de passe</label>
                        <input
                            id="new_password_confirmation"
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                    </div>
                    <div class="flex justify-end space-x-2 pt-4">
                        <button
                            type="button"
                            @click="showPasswordModal = false"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded"
                        >
                            Changer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    user: Object
})

const errors = usePage().props.errors
const processing = ref(false)
const showPasswordModal = ref(false)

const form = useForm({
    last_name: props.user.last_name,
    first_name: props.user.first_name,
    email: props.user.email,
})

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const formatDate = (dateString) => {
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    })
}

const updateAccount = () => {
    processing.value = true
    form.put('/account', {
        onFinish: () => {
            processing.value = false
        },
    })
}

const updatePassword = () => {
    // Fonctionnalité à implémenter
    alert('Fonctionnalité de changement de mot de passe à implémenter')
    showPasswordModal.value = false
}
</script>