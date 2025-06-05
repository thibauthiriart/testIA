import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configuration Axios pour Inertia
window.axios.defaults.headers.common['Accept'] = 'text/html, application/xhtml+xml';
window.axios.defaults.headers.common['X-Inertia'] = true;
