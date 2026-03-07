import { createI18n } from 'vue-i18n';
import en from '../locales/en.json';
import bn from '../locales/bn.json';

const messages = {
    en,
    bn,
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
    }
}

export function getLocale() {
    return i18n.global.locale.value;
}

export default i18n;