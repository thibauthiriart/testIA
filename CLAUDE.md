# Claude Project Rules

## Code Organization and Best Practices

### Component Factorization
- **ALWAYS** factorize recurring code into reusable components
- Create composables for shared logic (e.g., `useTableFilters` for table filtering logic)
- Extract common UI patterns into components:
  - `SortableHeader` for sortable table headers
  - `SearchInput` for search fields
  - `DataTable` for table structures
  - `Pagination` for pagination controls
  - `ItemsPerPage` for items per page selectors

### Vue.js Best Practices
- Use Composition API with `<script setup>`
- Prefer composables over mixins
- Extract complex logic into separate composable functions
- Use v-model for two-way binding in custom components

### Inertia.js Patterns
- Always use redirections for form submissions, not JSON responses
- Use `router.get/post/put/delete` with proper options:
  - `preserveState: true` to keep component state
  - `preserveScroll: true` to maintain scroll position
  - `only: ['data']` to reload specific data

### Code Style
- NO comments unless explicitly requested
- Keep components focused and single-purpose
- Use TypeScript-like prop definitions with proper types
- Consistent naming: camelCase for variables/functions, PascalCase for components
