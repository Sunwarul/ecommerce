import { reactive, watch } from 'vue';

const STORAGE_KEY = 'app_locale';
const SUPPORTED_LOCALES = ['en', 'bn', 'ar', 'es', 'hi'];

function getStoredLocale() {
    try {
        return localStorage.getItem(STORAGE_KEY);
    } catch (e) {
        return null;
    }
}

function setStoredLocale(locale) {
    try {
        localStorage.setItem(STORAGE_KEY, locale);
    } catch (e) {
        console.warn('Failed to store locale:', e);
    }
}

function getCookieLocale() {
    const matches = document.cookie.match(/locale=([^;]+)/);
    return matches ? matches[1] : null;
}

function getHtmlLocale() {
    return document.documentElement.getAttribute('data-locale');
}

function detectInitialLocale(serverLocale) {
    const storedLocale = getStoredLocale();
    const cookieLocale = getCookieLocale();
    const htmlLocale = getHtmlLocale();
    
    // Server locale takes priority - it's the most reliable source
    if (serverLocale && SUPPORTED_LOCALES.includes(serverLocale)) {
        return serverLocale;
    }
    
    // Fall back to cookie
    if (cookieLocale && SUPPORTED_LOCALES.includes(cookieLocale)) {
        return cookieLocale;
    }
    
    // Fall back to localStorage
    if (storedLocale && SUPPORTED_LOCALES.includes(storedLocale)) {
        return storedLocale;
    }
    
    // Fall back to HTML attribute
    if (htmlLocale && SUPPORTED_LOCALES.includes(htmlLocale)) {
        return htmlLocale;
    }
    
    return 'en';
}

export const localeStore = reactive({
    current: 'en',
    supported: SUPPORTED_LOCALES,
    initialized: false,
    
    init(serverLocale) {
        if (this.initialized) return;
        
        this.current = detectInitialLocale(serverLocale);
        setStoredLocale(this.current);
        this.initialized = true;
    },
    
    setLocale(locale) {
        if (!SUPPORTED_LOCALES.includes(locale)) {
            return;
        }
        
        this.current = locale;
        setStoredLocale(locale);
    },
    
    getLocale() {
        return this.current;
    }
});

export function isRtlLocale(locale) {
    return locale === 'ar';
}