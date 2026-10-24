import Cookies from 'js-cookie';
import { computed, reactive, watch } from 'vue';
import { applyDarkClass, loadThemeConfig, saveThemeConfig } from '@/Helpers/Theme';

// Appearance choices (persisted in localStorage, applied at boot in app.js)
const layoutConfig = reactive(loadThemeConfig());

const layoutState = reactive({
    staticMenuDesktopInactive: Cookies.get('menuState') === 'true' || false,
    overlayMenuActive: false,
    profileSidebarVisible: false,
    configSidebarVisible: false,
    staticMenuMobileActive: false,
    menuHoverActive: false,
    activeMenuItem: null,
});

watch(layoutConfig, (cfg) => saveThemeConfig(cfg), { deep: true });

export function useLayout() {
    const setActiveMenuItem = (item) => {
        layoutState.activeMenuItem = item.value || item;
    };

    const setDarkMode = (dark) => {
        const run = () => {
            layoutConfig.darkTheme = !!dark;
            applyDarkClass(layoutConfig.darkTheme);
        };
        if (document.startViewTransition && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.startViewTransition(run);
        } else {
            run();
        }
    };

    const toggleDarkMode = () => setDarkMode(!layoutConfig.darkTheme);

    const isDesktop = () => window.innerWidth > 991;

    const toggleMenu = () => {
        if (!isDesktop()) {
            layoutState.staticMenuMobileActive = !layoutState.staticMenuMobileActive;
            return;
        }
        if (layoutConfig.menuMode === 'overlay') {
            layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
            return;
        }
        layoutState.staticMenuDesktopInactive = !layoutState.staticMenuDesktopInactive;
        Cookies.set('menuState', layoutState.staticMenuDesktopInactive, { sameSite: 'lax', expires: 365 });
    };

    /** Close overlay / mobile sidebars (e.g. after navigating). */
    const closeMenus = () => {
        layoutState.overlayMenuActive = false;
        layoutState.staticMenuMobileActive = false;
        layoutState.menuHoverActive = false;
    };

    const setMenuMode = (mode) => {
        layoutConfig.menuMode = mode;
        closeMenus();
    };

    const isSidebarActive = computed(() => layoutState.overlayMenuActive || layoutState.staticMenuMobileActive);

    const isDarkTheme = computed(() => layoutConfig.darkTheme);

    const getPrimary = computed(() => layoutConfig.primary);

    const getSurface = computed(() => layoutConfig.surface);

    return {
        layoutConfig,
        layoutState,
        toggleMenu,
        closeMenus,
        setMenuMode,
        isSidebarActive,
        isDarkTheme,
        getPrimary,
        getSurface,
        setActiveMenuItem,
        toggleDarkMode,
        setDarkMode,
    };
}
