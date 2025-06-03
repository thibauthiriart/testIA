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

            <!-- Population Filter -->
            <PopulationFilter
              class="mt-6"
              :filtered-count="filteredCities.length"
              @filter-changed="handleFilterChange"
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
import { ref, onMounted, computed, createApp } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import MapPopup from '@/components/Map/Popup.vue'
import MapMarker from '@/components/Map/Marker.vue'
import PopulationFilter from '@/components/PopulationFilter.vue'
import MapStats from '@/components/MapStats.vue'
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
const markers = ref([])

// Population filter state
const currentPopulationFilter = ref({ min: 0, max: Infinity })

// Computed properties for filtered cities
const filteredCities = computed(() => {
  const { min, max } = currentPopulationFilter.value
  return cities.value.filter(city => 
    city.population >= min && city.population <= max
  )
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

// Handle filter change from PopulationFilter component
const handleFilterChange = (filter) => {
  currentPopulationFilter.value = filter
  updateMarkers()
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