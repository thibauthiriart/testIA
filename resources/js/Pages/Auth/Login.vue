<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8 transition-colors duration-300">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900 dark:text-white">
                    Connexion à votre compte
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600 dark:text-gray-300">
                    Ou
                    <Link href="/register" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300">
                        créez un nouveau compte
                    </Link>
                </p>
            </div>
            <form class="mt-8 space-y-6" @submit.prevent="login">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email" class="sr-only">Adresse email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Adresse email"
                        />
                        <div v-if="errors.email" class="text-red-600 text-sm mt-1">{{ errors.email }}</div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">Mot de passe</label>
                        <input
                            id="password"
                            v-model="form.password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
                            placeholder="Mot de passe"
                        />
                        <div v-if="errors.password" class="text-red-600 text-sm mt-1">{{ errors.password }}</div>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="processing"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50"
                    >
                        <span v-if="processing">Connexion en cours...</span>
                        <span v-else>Se connecter</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'

const errors = usePage().props.errors

const form = useForm({
    email: '',
    password: '',
})

const processing = ref(false)

const login = () => {
    processing.value = true
    form.post('/login', {
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>