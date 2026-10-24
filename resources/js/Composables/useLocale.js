import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { useToast } from 'primevue/usetoast';
import { setLocale, getLocale, isRtl as isRtlLocale, i18n } from '../Plugins/i18n';
import { localeStore, LOCALE_NAMES } from '../Stores/localeStore';

export function useLocale() {
    const page = usePage();
    const toast = useToast();

    const currentLocale = computed({
        get: () => localeStore.current,
        set: (val) => localeStore.setLocale(val),
    });

    const supportedLocales = computed(() => page.props.locale?.supported || localeStore.supported);

    const initLocale = () => {
        const serverLocale = page.props.locale?.current;

        if (!localeStore.initialized) {
            localeStore.init(serverLocale);
        }

        const storedLocale = localeStore.current;
        if (storedLocale && storedLocale !== getLocale()) {
            setLocale(storedLocale);
        }
    };

    /**
     * Persist the language on the server (session, cookie and user profile), switch the UI and
     * direction immediately, then reload so server-rendered texts (flash, validation) follow.
     */
    const changeLocale = async (locale) => {
        if (!supportedLocales.value.includes(locale) || locale === localeStore.current) {
            return;
        }

        try {
            const { data } = await axios.post(route('locale.set'), { locale }, { headers: { Accept: 'application/json' } });
            if (!data?.success) throw new Error('Locale not saved');
            setLocale(locale);
            window.location.reload();
        } catch (e) {
            toast.add({
                severity: 'error',
                summary: i18n.global.t('shared.terms.error'),
                detail: i18n.global.t('layout.language_failed'),
                life: 4000,
            });
        }
    };

    const isRtl = computed(() => isRtlLocale(currentLocale.value));

    const localeName = computed(() => LOCALE_NAMES[currentLocale.value] || currentLocale.value);

    return {
        currentLocale,
        supportedLocales,
        initLocale,
        changeLocale,
        isRtl,
        localeName,
    };
}
