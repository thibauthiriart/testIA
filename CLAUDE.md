# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

### Development
```bash
composer dev        # Start all services (PHP server, queue, logs, Vite)
npm run dev         # Start Vite dev server only (if needed separately)
php artisan serve   # Start PHP server only (if needed separately)
```

### Testing
```bash
php artisan test                 # Run all tests
php artisan test --parallel      # Run tests in parallel
php artisan test path/to/test    # Run specific test file
composer test                    # Clear config and run tests
```

### Code Quality
```bash
../vendor/bin/pint          # Fix PHP code style
../vendor/bin/pint --test   # Check PHP code style without fixing
```

### Build & Deployment
```bash
npm run build         # Build production assets
php artisan optimize  # Cache config, routes, views
```

## Architecture Overview

### Stack
- **Backend**: Laravel 12.x + Inertia.js for server-side routing
- **Frontend**: Vue 3 with Composition API (`<script setup>`)
- **Database**: SQLite (dev/test), configurable for production
- **Auth**: Laravel Passport (OAuth2) + Spatie Permissions (RBAC)

### Key Patterns

#### 1. Inertia.js Flow
Controllers return Inertia responses that render Vue pages:
```php
return Inertia::render('Cities/Index', ['cities' => $cities]);
```

#### 2. Component Organization
- **Pages**: Feature-based organization in `resources/js/Pages/`
- **Components**: Reusable UI in `resources/js/components/`
- **Composables**: Shared logic in `resources/js/composables/`

#### 3. Form Handling
Always use redirections for form submissions:
```javascript
router.post('/cities', formData, {
    preserveState: true,
    preserveScroll: true
});
```

#### 4. Validation
Separate Form Request classes for validation:
- `StoreCityRequest`, `UpdateCityRequest`, etc.
- Index requests use dedicated classes like `IndexCityRequest`

#### 5. Authorization
- Middleware: `CheckRole` for route-level protection
- Policy classes for model-level authorization
- Roles: admin, editor, viewer

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