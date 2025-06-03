import { ref, computed, watch, readonly } from 'vue'

// État global partagé
const isDark = ref(false)

// Mettre à jour la classe sur le document
const updateTheme = () => {
  if (typeof document !== 'undefined') {
    if (isDark.value) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
  }
}

// Initialiser le thème immédiatement si on est côté client
const initializeTheme = () => {
  if (typeof window !== 'undefined' && typeof localStorage !== 'undefined') {
    // D'abord, vérifier si la classe dark est déjà présente (script initial)
    const hasClassDark = document.documentElement.classList.contains('dark')
    const savedTheme = localStorage.getItem('theme')
    
    if (savedTheme) {
      isDark.value = savedTheme === 'dark'
    } else if (hasClassDark) {
      // Si la classe dark est déjà là, synchroniser l'état
      isDark.value = true
    } else {
      // Utiliser la préférence système
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    
    updateTheme()
  }
}

// Watcher pour sauvegarder les changements ET mettre à jour le DOM
watch(isDark, (newValue) => {
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('theme', newValue ? 'dark' : 'light')
  }
  updateTheme()
}, { immediate: false })

// Initialiser automatiquement
if (typeof window !== 'undefined') {
  initializeTheme()
}

export function useTheme() {
  const toggleTheme = () => {
    isDark.value = !isDark.value
  }

  const setTheme = (theme) => {
    isDark.value = theme === 'dark'
  }

  const currentTheme = computed(() => isDark.value ? 'dark' : 'light')

  return {
    isDark: readonly(isDark),
    currentTheme,
    toggleTheme,
    setTheme,
    initializeTheme
  }
}