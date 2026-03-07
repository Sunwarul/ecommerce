import { createI18n } from 'vue-i18n';
import en from '../locales/en.json';
import bn from '../locales/bn.json';
import ar from '../locales/ar.json';
import es from '../locales/es.json';
import hi from '../locales/hi.json';
import { localeStore } from '../Stores/localeStore';

const messages = {
    en,
    bn,
    ar,
    es,
    hi,
};

export const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages,
});

export function setLocale(locale) {
    if (messages[locale]) {
        i18n.global.locale.value = locale;
        localeStore.setLocale(locale);
        
        document.documentElement.dir = locale === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.lang = locale;
    }
}

export function initI18n(serverLocale) {
    // Server locale should always take priority - it's the most reliable source
    const detectedLocale = serverLocale || localeStore.current || 'en';
    
    if (messages[detectedLocale]) {
        i18n.global.locale.value = detectedLocale;
        localeStore.init(serverLocale);
        
        document.documentElement.dir = detectedLocale === 'ar' ? 'rtl' : 'ltr';
        document.documentElement.lang = detectedLocale;
    }
}

export function getLocale() {
    return i18n.global.locale.value;
}

export default i18n;