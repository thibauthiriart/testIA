import { useAuthStore } from '../stores/auth.js'
import { router } from '@inertiajs/vue3'

export const useApi = () => {
    const authStore = useAuthStore()

    const apiRequest = async (url, options = {}) => {
        const authHeaders = authStore.getAuthHeaders()
        console.log('API Request to:', url, 'Token present:', !!authStore.token, 'Headers:', authHeaders)

        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...authHeaders,
                ...options.headers
            },
            ...options
        }

        try {
            const response = await fetch(url, config)

            if (response.status === 401) {
                // Token invalide ou expiré, rediriger vers login
                authStore.clearToken()
                router.visit('/login')
                throw new Error('Non autorisé')
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const contentType = response.headers.get('content-type')
            if (contentType && contentType.includes('application/json')) {
                return await response.json()
            }

            return await response.text()
        } catch (error) {
            console.error('API Request Error:', error)
            throw error
        }
    }

    const get = (url, params = {}) => {
        const urlWithParams = new URL(url, window.location.origin)
        Object.keys(params).forEach(key => {
            if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
                urlWithParams.searchParams.append(key, params[key])
            }
        })
        return apiRequest(urlWithParams.toString())
    }

    const post = (url, data = {}) => {
        return apiRequest(url, {
            method: 'POST',
            body: JSON.stringify(data)
        })
    }

    const put = (url, data = {}) => {
        return apiRequest(url, {
            method: 'PUT',
            body: JSON.stringify(data)
        })
    }

    const del = (url) => {
        return apiRequest(url, {
            method: 'DELETE'
        })
    }

    return {
        get,
        post,
        put,
        delete: del
    }
}
