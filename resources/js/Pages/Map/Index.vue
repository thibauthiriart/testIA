<template>
  <AppLayout title="Carte des Villes">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
        Carte de France - Villes
      </h2>
    </template>

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
            
            <!-- Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-blue-50 dark:bg-blue-900 p-4 rounded-lg transition-colors duration-300">
                <div class="text-lg font-semibold text-blue-900 dark:text-blue-100">{{ cities.length }}</div>
                <div class="text-sm text-blue-700 dark:text-blue-300">Villes affichées</div>
              </div>
              <div class="bg-green-50 dark:bg-green-900 p-4 rounded-lg transition-colors duration-300">
                <div class="text-lg font-semibold text-green-900 dark:text-green-100">{{ totalPopulation.toLocaleString() }}</div>
                <div class="text-sm text-green-700 dark:text-green-300">Population totale</div>
              </div>
              <div class="bg-purple-50 dark:bg-purple-900 p-4 rounded-lg transition-colors duration-300">
                <div class="text-lg font-semibold text-purple-900 dark:text-purple-100">{{ uniqueDepartments }}</div>
                <div class="text-sm text-purple-700 dark:text-purple-300">Départements</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed, createApp } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MapPopup from '@/components/Map/Popup.vue'
import MapMarker from '@/components/Map/Marker.vue'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

// Fix for default markers in Leaflet with Vue/Vite
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: new URL('leaflet/dist/images/marker-icon-2x.png', import.meta.url).href,
  iconUrl: new URL('leaflet/dist/images/marker-icon.png', import.meta.url).href,
  shadowUrl: new URL('leaflet/dist/images/marker-shadow.png', import.meta.url).href,
})

const mapContainer = ref(null)
const loading = ref(true)
const cities = ref([])
const map = ref(null)

// Computed properties for stats
const totalPopulation = computed(() => {
  return cities.value.reduce((total, city) => total + city.population, 0)
})

const uniqueDepartments = computed(() => {
  const departments = new Set(cities.value.map(city => city.department.code))
  return departments.size
})

// Fetch cities data
const fetchCities = async () => {
  try {
    const response = await fetch('/api/map/cities', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        'Accept': 'application/json',
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
  
  // Add markers for each city
  cities.value.forEach(city => {
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
  fetchCities()
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

/* Loading animation */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>