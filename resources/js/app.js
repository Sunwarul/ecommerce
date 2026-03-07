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
import i18n, { setLocale } from "./Plugins/i18n";
const appName = import.meta.env.VITE_APP_NAME || "E-Commerce";

function getLocaleFromCookie() {
    const matches = document.cookie.match(/locale=([^;]+)/);
    return matches ? matches[1] : null;
}

function getLocaleFromHtml() {
    const html = document.documentElement;
    return html.getAttribute('data-locale');
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
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

        const supportedLocales = ['en', 'bn', 'ar', 'es', 'hi'];

        const serverLocale = props.locale?.current;
        const cookieLocale = getLocaleFromCookie();
        const htmlLocale = getLocaleFromHtml();

        console.log("Props Keys:", Object.keys(props || {}));
        console.log("Server Locale (from props):", serverLocale);
        console.log("Cookie Locale:", cookieLocale);
        console.log("HTML Locale:", htmlLocale);

        const finalLocale = serverLocale || cookieLocale || htmlLocale || 'en';

        console.log("Final Locale:", finalLocale);

        if (finalLocale && supportedLocales.includes(finalLocale)) {
            i18n.global.locale.value = finalLocale;
        }

        app.config.globalProperties.$resolveImagePath = resolveImagePath;

        return app.mount(el);
    },

    progress: {
        color: "#4B5563",
    },
});
