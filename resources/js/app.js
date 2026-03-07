import "primeicons/primeicons.css";
import "../css/app.scss";
import "./bootstrap";
import Theme from "./Helpers/Theme";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import PrimeVue from "primevue/config";

import ConfirmationService from "primevue/confirmationservice";
import DialogService from "primevue/dialogservice";
import ToastService from "primevue/toastservice";
import AppState from "./Helpers/AppState";
import { resolveImagePath } from "./Helpers/ResolveImage";
import i18n, { initI18n, setLocale } from "./Plugins/i18n";
import { localeStore } from "./Stores/localeStore";

const appName = import.meta.env.VITE_APP_NAME || "E-Commerce";

function syncLocaleOnNavigation(visit) {
    const storedLocale = localeStore.current;
    if (storedLocale && storedLocale !== i18n.global.locale.value) {
        setLocale(storedLocale);
    }
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    onNavigate: (visit) => {
        syncLocaleOnNavigation(visit);
    },
    resolve: (name) => {
        const [module, page] = name.split("::");
        if (page) {
            return resolvePageComponent(
                `../../Modules/${module}/resources/js/Pages/${page}.vue`,
                import.meta.glob("../../Modules/**/resources/js/Pages/**/*.vue")
            );
        } else {
            return resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob("./Pages/**/*.vue")
            );
        }
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .use(ConfirmationService)
            .use(ToastService)
            .use(DialogService)
            .use(AppState)
            .use(PrimeVue, {
                theme: {
                    preset: Theme,
                    options: {
                        darkModeSelector: ".app-dark",
                    },
                },
            });

        const serverLocale = props.locale?.current;
        
        // Always initialize with server locale when available
        // This ensures page reloads after language switch work correctly
        if (!localeStore.initialized) {
            initI18n(serverLocale);
        } else {
            // On subsequent navigations, sync with stored locale
            const currentStored = localeStore.current;
            if (currentStored && currentStored !== i18n.global.locale.value) {
                setLocale(currentStored);
            }
        }

        app.config.globalProperties.$resolveImagePath = resolveImagePath;

        return app.mount(el);
    },

    progress: {
        color: "#4B5563",
    },
});