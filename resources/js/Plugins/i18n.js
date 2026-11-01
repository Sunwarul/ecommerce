import { createI18n } from 'vue-i18n';
import { localeStore } from '../Stores/localeStore';

/*
 * Messages are assembled from:
 *   locales/{lang}.json              -> shared keys (common, menu, auth, ...)
 *   locales/{lang}/{namespace}.json  -> one file per feature area, available as t('{namespace}.key')
 * English is the fallback for any missing key.
 */
const base = import.meta.glob('../locales/*.json', { eager: true, import: 'default' });
const namespaced = import.meta.glob('../locales/*/*.json', { eager: true, import: 'default' });

export const SUPPORTED_LOCALES = ['en', 'bn', 'ar', 'es', 'hi'];
export const RTL_LOCALES = ['ar'];

function deepMerge(target, source) {
    for (const [k, v] of Object.entries(source || {})) {
        if (v && typeof v === 'object' && !Array.isArray(v)) {
            target[k] = deepMerge(target[k] && typeof target[k] === 'object' ? target[k] : {}, v);
        } else {
            target[k] = v;
        }
    }
    return target;
}

const messages = {};
for (const [path, data] of Object.entries(base)) {
    const lang = path.match(/locales\/(\w+)\.json$/)[1];
    messages[lang] = deepMerge(messages[lang] || {}, data);
}
for (const [path, data] of Object.entries(namespaced)) {
    const [, lang, ns] = path.match(/locales\/(\w+)\/([\w-]+)\.json$/);
    messages[lang] = messages[lang] || {};
    messages[lang][ns] = deepMerge(messages[lang][ns] || {}, data);
}

export const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages,
    missingWarn: false,
    fallbackWarn: false,
});

export function isRtl(locale) {
    return RTL_LOCALES.includes(locale);
}

function applyDocumentLocale(locale) {
    const dir = isRtl(locale) ? 'rtl' : 'ltr';
    document.documentElement.dir = dir;
    document.documentElement.lang = locale;
    document.documentElement.dataset.locale = locale;
}

export function setLocale(locale) {
    if (messages[locale]) {
        i18n.global.locale.value = locale;
        localeStore.setLocale(locale);
        applyDocumentLocale(locale);
    }
}

export function initI18n(serverLocale) {
    // Server locale should always take priority - it's the most reliable source
    const detectedLocale = serverLocale || localeStore.current || 'en';

    if (messages[detectedLocale]) {
        i18n.global.locale.value = detectedLocale;
        localeStore.init(serverLocale);
        applyDocumentLocale(detectedLocale);
    }
}

export function getLocale() {
    return i18n.global.locale.value;
}

export default i18n;
