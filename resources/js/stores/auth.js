import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('auth_token') || null)
  const isAuthenticated = ref(!!token.value)
  const isReady = ref(false)

  const setToken = (newToken) => {
    token.value = newToken
    isAuthenticated.value = !!newToken
    isReady.value = true
    
    if (newToken) {
      localStorage.setItem('auth_token', newToken)
    } else {
      localStorage.removeItem('auth_token')
    }
  }

  const markAsReady = () => {
    isReady.value = true
  }

  const clearToken = () => {
    setToken(null)
  }

  const getAuthHeaders = () => {
    if (!token.value) return {}
    
    return {
      'Authorization': `Bearer ${token.value}`,
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    }
  }

  return {
    token,
    isAuthenticated,
    isReady,
    setToken,
    clearToken,
    markAsReady,
    getAuthHeaders
  }
})