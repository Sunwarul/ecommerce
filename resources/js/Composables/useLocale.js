import { ref, computed, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { setLocale, getLocale } from '../Plugins/i18n';
import { localeStore } from '../Stores/localeStore';

export function useLocale() {
    const page = usePage();
    
    const currentLocale = computed({
        get: () => localeStore.current,
        set: (val) => localeStore.setLocale(val)
    });
    
    const supportedLocales = computed(() => {
        return page.props.locale?.supported || localeStore.supported;
    });

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

    const changeLocale = async (locale) => {
        if (!supportedLocales.value.includes(locale)) {
            console.warn('[useLocale] Unsupported locale:', locale);
            return;
        }

        try {
            const csrfToken = page.props.csrf_token;
            if (!csrfToken) {
                console.error('[useLocale] CSRF token not found');
                return;
            }

            const response = await fetch(route('locale.set'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ locale }),
            });

            const data = await response.json();
            if (data.success) {
                setLocale(locale);
                localeStore.setLocale(locale);
                
                window.location.reload();
            }
        } catch (error) {
            console.error('[useLocale] Failed to change locale:', error);
        }
    };

    const isRtl = computed(() => {
        return currentLocale.value === 'ar';
    });

    const localeName = computed(() => {
        const names = {
            en: 'English',
            bn: 'বাংলা',
            ar: 'العربية',
            es: 'Español',
            hi: 'हिन्दी',
        };
        return names[currentLocale.value] || currentLocale.value;
    });

    return {
        currentLocale,
        supportedLocales,
        initLocale,
        changeLocale,
        isRtl,
        localeName,
    };
}