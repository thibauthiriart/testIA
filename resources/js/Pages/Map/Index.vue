<template>
  <AppLayout title="Carte des Villes">

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg transition-colors duration-300">
          <div class="p-6">
            <div class="mb-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Carte interactive des villes françaises
              </h3>
              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Survolez les marqueurs pour voir les informations des villes
              </p>
            </div>

            <!-- Map Container -->
            <div
              ref="mapContainer"
              id="map"
              class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-600"
              style="height: 600px;"
            >
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center h-96">
              <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 dark:border-indigo-400"></div>
            </div>

            <!-- City Search -->
            <CitySearch
              class="mt-6"
              :cities="cities"
              @city-selected="handleCitySelected"
              @properties-found="handlePropertiesFound"
            />

            <!-- Stats -->
            <MapStats
              class="mt-6"
              :cities-count="filteredCities.length"
              :total-population="totalPopulation"
              :departments-count="uniqueDepartments"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed, createApp, watch } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MapPopup from '@/components/Map/Popup.vue'
import MapMarker from '@/components/Map/Marker.vue'
import PropertyPopup from '@/components/Map/PropertyPopup.vue'
import CitySearch from '@/components/cities/CitySearch.vue'
import MapStats from '@/components/Map/MapStats.vue'
import { useAuthStore } from '../../stores/auth.js'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

// Fix for default markers in Leaflet with Vue/Vite
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
})

const authStore = useAuthStore()
const mapContainer = ref(null)
const loading = ref(true)
const cities = ref([])
const map = ref(null)
const markers = ref([])
const propertyMarkers = ref([])

// Search state
const selectedCity = ref(null)
const searchProperties = ref([])

// Computed properties for filtered cities (now just all cities by default)
const filteredCities = computed(() => {
  return cities.value
})

// Computed properties for stats
const totalPopulation = computed(() => {
  return filteredCities.value.reduce((total, city) => total + city.population, 0)
})

const uniqueDepartments = computed(() => {
  const departments = new Set(filteredCities.value.map(city => city.department.code))
  return departments.size
})

// Fetch cities data
const fetchCities = async () => {
  try {
    const response = await fetch('/api/map/cities', {
      method: 'GET',
      headers: {
        ...authStore.getAuthHeaders(),
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      },
      credentials: 'same-origin'
    })

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`)
    }

    const data = await response.json()
    cities.value = data
    loading.value = false

    // Initialize map after data is loaded
    if (data.length > 0) {
      initializeMap()
    }
  } catch (error) {
    console.error('Error fetching cities:', error)
    loading.value = false
  }
}

// Initialize the Leaflet map
const initializeMap = () => {
  // Initialize map centered on France
  map.value = L.map('map').setView([46.603354, 1.888334], 6)

  // Add OpenStreetMap tiles (always the same)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map.value)

  // Initialize with all cities
  updateMarkers()
}

// Update markers based on filtered cities
const updateMarkers = () => {
  // Clear existing markers
  markers.value.forEach(marker => {
    map.value.removeLayer(marker)
  })
  markers.value = []

  // Add markers for filtered cities
  filteredCities.value.forEach(city => {
    // Create marker container
    const markerContainer = document.createElement('div')
    const markerApp = createApp(MapMarker, { population: city.population })
    markerApp.mount(markerContainer)

    // Get icon size for positioning
    const iconSize = getIconSize(city.population)

    const marker = L.marker([city.latitude, city.longitude], {
      icon: L.divIcon({
        className: 'custom-div-icon',
        html: markerContainer.outerHTML,
        iconSize: [iconSize, iconSize],
        iconAnchor: [iconSize / 2, iconSize / 2]
      })
    }).addTo(map.value)

    // Create popup content using Vue component
    const popupContainer = document.createElement('div')
    const popupApp = createApp(MapPopup, { city })
    popupApp.mount(popupContainer)

    marker.bindPopup(popupContainer)

    // Add hover events for better UX
    marker.on('mouseover', function() {
      this.openPopup()
    })

    marker.on('mouseout', function() {
      this.closePopup()
    })

    markers.value.push(marker)
  })
}

// Handle city selection from CitySearch component
const handleCitySelected = (city) => {
  selectedCity.value = city
  if (city && map.value) {
    // Center map on selected city
    map.value.setView([city.latitude, city.longitude], 12)
  }
}

// Handle properties found from CitySearch component
const handlePropertiesFound = (properties) => {
  searchProperties.value = properties
  updatePropertyMarkers()
}

// Update property markers on the map
const updatePropertyMarkers = () => {
  // Clear existing property markers
  propertyMarkers.value.forEach(marker => {
    map.value.removeLayer(marker)
  })
  propertyMarkers.value = []

  // Add markers for properties
  searchProperties.value.forEach(property => {
    if (property.latitude && property.longitude) {
      const marker = L.marker([property.latitude, property.longitude], {
        icon: L.divIcon({
          className: 'property-marker',
          html: `<div class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold shadow-lg">€</div>`,
          iconSize: [24, 24],
          iconAnchor: [12, 12]
        })
      }).addTo(map.value)

      // Create popup container for property
      const popupContainer = document.createElement('div')
      const propertyPopupApp = createApp(PropertyPopup, { property })
      propertyPopupApp.mount(popupContainer)

      marker.bindPopup(popupContainer, {
        maxWidth: 300,
        minWidth: 200
      })

      propertyMarkers.value.push(marker)
    }
  })
}

// Get icon size based on population (for positioning only)
const getIconSize = (population) => {
  if (population > 500000) return 16
  if (population > 100000) return 14
  if (population > 50000) return 12
  if (population > 20000) return 10
  return 8
}


onMounted(() => {
  // Si le token est déjà présent, on peut charger immédiatement
  if (authStore.isReady || authStore.token) {
    fetchCities()
  } else {
    // Sinon on attend que le store soit prêt
    const unwatch = watch(() => authStore.isReady, (isReady) => {
      if (isReady) {
        fetchCities()
        unwatch()
      }
    })

    // Fallback: marquer comme prêt après 500ms même sans token (pour les cas où on est déjà connecté)
    setTimeout(() => {
      if (!authStore.isReady) {
        authStore.markAsReady()
      }
    }, 500)
  }
})
</script>

<style scoped>
/* Custom styles for the map */
#map {
  z-index: 1;
}

/* Fix for Leaflet CSS with Tailwind */
:global(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

:global(.leaflet-popup-tip) {
  background: white;
}

/* Dark theme support for popups */
:global(.dark .leaflet-popup-content-wrapper) {
  background-color: #374151;
  color: white;
}

:global(.dark .leaflet-popup-tip) {
  background: #374151;
}

:global(.custom-div-icon) {
  background: transparent !important;
  border: none !important;
}
</style>
