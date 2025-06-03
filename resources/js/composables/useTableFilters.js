import { router } from '@inertiajs/vue3'

export function useTableFilters(route, initialFilters = {}) {
    const buildQueryParams = (overrides = {}) => {
        return {
            search: initialFilters.search || '',
            department_search: initialFilters.department_search || '',
            department_id: initialFilters.department_id || '',
            population_operator: initialFilters.population_operator || '',
            population_value: initialFilters.population_value || '',
            sort: initialFilters.sort || 'name',
            direction: initialFilters.direction || 'asc',
            per_page: initialFilters.per_page || 10,
            ...overrides
        }
    }

    const applyFilters = (params = {}, options = {}) => {
        const defaultOptions = {
            preserveState: true,
            preserveScroll: true,
            only: ['cities', 'filters']
        }
        
        router.get(route, buildQueryParams(params), {
            ...defaultOptions,
            ...options
        })
    }

    const handleSort = (field, currentSort, currentDirection) => {
        let direction = 'asc'
        
        if (currentSort === field) {
            direction = currentDirection === 'asc' ? 'desc' : 'asc'
        }
        
        applyFilters({ sort: field, direction })
    }

    const resetFilters = () => {
        router.get(route, {}, {
            preserveState: true,
            preserveScroll: true,
            only: ['cities', 'filters']
        })
    }

    return {
        buildQueryParams,
        applyFilters,
        handleSort,
        resetFilters
    }
}