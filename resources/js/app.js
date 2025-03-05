import './bootstrap';
import '../css/app.scss';
import 'primeicons/primeicons.css'


import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import PrimeVue from 'primevue/config';

import ConfirmationService from 'primevue/confirmationservice';
import DialogService from 'primevue/dialogservice';
import ToastService from 'primevue/toastservice';
import Noir from './primevue/Noir';
import AppState from './primevue/AppState';

const appName = import.meta.env.VITE_APP_NAME || 'E-Commerce';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => {
        const [module, page] = name.split('::');
        if (page) {
            return resolvePageComponent(
                `../../Modules/${module}/resources/js/Pages/${page}.vue`,
                import.meta.glob("../../Modules/**/resources/js/Pages/**/*.vue")
            );
        } else {
            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue'),
            )
        }

    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(ConfirmationService)
            .use(ToastService)
            .use(DialogService)
            .use(AppState)
            .use(PrimeVue, {
                theme: {
                    preset: Noir,
                    options: {
                        darkModeSelector: '.app-dark'
                    }
                }
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
