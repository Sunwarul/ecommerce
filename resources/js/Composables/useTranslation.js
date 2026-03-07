import { useI18n } from 'vue-i18n';

export function useTranslation() {
    const { t, locale } = useI18n();

    const translate = (key, params = {}) => {
        return t(key, params);
    };

    const setLocale = (newLocale) => {
        locale.value = newLocale;
    };

    return {
        t: translate,
        locale,
        setLocale,
    };
}