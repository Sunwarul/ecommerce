import React from 'react';
import ReactDOM from 'react-dom/client';
import 'primeicons/primeicons.css';
import { PrimeReactProvider } from 'primereact/api';
// import 'primeflex/primeflex.css';
import 'primereact/resources/primereact.css';
import 'primereact/resources/themes/lara-light-indigo/theme.css';

// Custom CSS - Import AFTER PrimeReact theme
import './bootstrap';
import '../css/app.scss';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

const appName = import.meta.env.VITE_APP_NAME || 'E-Commerce';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => {
        const [module, page] = name.split('::');
        if (page) {
            return resolvePageComponent(
                `../../Modules/${module}/resources/js/Pages/${page}.jsx`,
                import.meta.glob("../../Modules/**/resources/js/Pages/**/*.jsx")
            );
        } else {
            return resolvePageComponent(
                `./Pages/${name}.jsx`,
                import.meta.glob('./Pages/**/*.jsx'),
            )
        }
    },
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <PrimeReactProvider>
                <App {...props} />
            </PrimeReactProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

