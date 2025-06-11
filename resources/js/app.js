import '../css/app.css';
import './bootstrap';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Global error handling
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response?.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('userRole');
            window.location.href = '/login';
        }
        if (error.response?.status === 403) {
            alert('You do not have permission to perform this action.');
        }
        if (error.response?.status === 419) {
            alert('The page has expired. Please refresh and try again.');
        }
        if (error.response?.status === 500) {
            alert('An unexpected error occurred. Please try again later.');
        }
        return Promise.reject(error);
    }
);

// Global loading state
let loadingTimeout;
axios.interceptors.request.use(
    config => {
        // const token = localStorage.getItem('token');
        // if (token) {
        //     config.headers.Authorization = `Bearer ${token}`;
        // }
        
        loadingTimeout = setTimeout(() => {
            document.body.classList.add('loading');
        }, 500);
        return config;
    },
    error => {
        clearTimeout(loadingTimeout);
        document.body.classList.remove('loading');
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    response => {
        clearTimeout(loadingTimeout);
        document.body.classList.remove('loading');
        return response;
    },
    error => {
        clearTimeout(loadingTimeout);
        document.body.classList.remove('loading');
        return Promise.reject(error);
    }
);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        
        app.use(plugin)
           .use(ZiggyVue)
           .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
