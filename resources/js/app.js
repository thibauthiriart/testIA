import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { useSecureRequests } from './composables/useSecureRequests.js';
import { useAuthStore } from './stores/auth.js';

const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - Cities & Departments`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(pinia);

        // Récupérer le token depuis les props Inertia
        const authStore = useAuthStore();
        if (props.initialPage.props.flash?.token) {
            authStore.setToken(props.initialPage.props.flash.token);
        } else {
            authStore.markAsReady();
        }

        // Initialiser les intercepteurs de sécurité
        const secureRequests = useSecureRequests();
        secureRequests.init();

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});