import { router } from '@inertiajs/vue3'
import { useAuthStore } from '../stores/auth.js'

export const useSecureRequests = () => {
    const authStore = useAuthStore()

    // Pour les routes web, le middleware backend gère l'authentification via cookies de session
    // Pas besoin d'intercepter Inertia
    const setupInertiaInterceptor = () => {
        // Le middleware AuthenticateWithTokenOrSession gère l'authentification backend
        // Les sessions Laravel fonctionnent déjà
    }

    // Surcharger fetch global pour ajouter automatiquement les tokens
    const setupFetchInterceptor = () => {
        const originalFetch = window.fetch

        window.fetch = async (url, options = {}) => {
            // Si c'est une requête vers notre API et qu'on a un token
            if (url.startsWith('/api') && authStore.token) {
                options.headers = {
                    'Authorization': `Bearer ${authStore.token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...options.headers
                }
            }

            try {
                const response = await originalFetch(url, options)

                // Si 401, token invalide
                if (response.status === 401 && url.startsWith('/api')) {
                    authStore.clearToken()
                    router.visit('/login')
                    throw new Error('Token invalide')
                }

                return response
            } catch (error) {
                throw error
            }
        }
    }

    // Surcharger axios si utilisé
    const setupAxiosInterceptor = () => {
        if (window.axios) {
            // Intercepteur de requête
            window.axios.interceptors.request.use(
                (config) => {
                    if (authStore.token) {
                        config.headers.Authorization = `Bearer ${authStore.token}`
                    }
                    return config
                },
                (error) => Promise.reject(error)
            )

            // Intercepteur de réponse
            window.axios.interceptors.response.use(
                (response) => response,
                (error) => {
                    if (error.response?.status === 401) {
                        authStore.clearToken()
                        router.visit('/login')
                    }
                    return Promise.reject(error)
                }
            )
        }
    }

    const init = () => {
        setupInertiaInterceptor()
        setupFetchInterceptor()
        setupAxiosInterceptor()
    }

    return {
        init
    }
}
