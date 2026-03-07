import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { setLocale } from '../Plugins/i18n';

const currentLocale = ref('en');
const supportedLocales = ref(['en', 'bn']);

export function useLocale() {
    const page = usePage();

    const initLocale = () => {
        const localeData = page.props.locale;
        if (localeData) {
            currentLocale.value = localeData.current || 'en';
            supportedLocales.value = localeData.supported || ['en', 'bn'];
            setLocale(currentLocale.value);
        }
    };

    const changeLocale = async (locale) => {
        if (!supportedLocales.value.includes(locale)) {
            return;
        }

        try {
            const response = await fetch(route('locale.set'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': page.props.csrf_token,
                },
                body: JSON.stringify({ locale }),
            });

            const data = await response.json();
            if (data.success) {
                currentLocale.value = locale;
                setLocale(locale);
            }
        } catch (error) {
            console.error('Failed to change locale:', error);
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