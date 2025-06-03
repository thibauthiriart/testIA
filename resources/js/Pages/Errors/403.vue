<template>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl sm:rounded-lg sm:px-10">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100">
                        <svg class="h-12 w-12 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    
                    <h1 class="mt-6 text-3xl font-extrabold text-gray-900">
                        Accès refusé
                    </h1>
                    
                    <p class="mt-2 text-sm text-gray-600">
                        Erreur 403 - Accès non autorisé
                    </p>
                    
                    <div class="mt-6">
                        <p class="text-base text-gray-700">
                            Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette page.
                        </p>
                        <p class="mt-2 text-sm text-gray-600">
                            Cette section est réservée aux administrateurs.
                        </p>
                    </div>
                    
                    <div class="mt-8 space-y-3">
                        <Link
                            href="/dashboard"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Retour au tableau de bord
                        </Link>
                        
                        <Link
                            href="/"
                            class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Retour à l'accueil
                        </Link>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Si vous pensez qu'il s'agit d'une erreur, veuillez contacter l'administrateur.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>