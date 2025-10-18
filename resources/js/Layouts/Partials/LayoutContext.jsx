import React, { createContext, useContext, useState, useMemo } from 'react';
import Cookies from 'js-cookie';

const LayoutContext = createContext();

export const LayoutProvider = ({ children }) => {
    const [layoutConfig, setLayoutConfig] = useState({
        preset: 'Aura',
        primary: 'emerald',
        surface: null,
        darkTheme: false,
        menuMode: 'static'
    });

    const [layoutState, setLayoutState] = useState({
        staticMenuDesktopInactive: Cookies.get('menuState') === 'true' || false,
        overlayMenuActive: false,
        profileSidebarVisible: false,
        configSidebarVisible: false,
        staticMenuMobileActive: false,
        menuHoverActive: false,
        activeMenuItem: null
    });

    const setActiveMenuItem = (item) => {
        setLayoutState(prev => ({
            ...prev,
            activeMenuItem: typeof item === 'object' ? item.value || item : item
        }));
    };

    const toggleDarkMode = () => {
        if (!document.startViewTransition) {
            executeDarkModeToggle();
            return;
        }
        document.startViewTransition(() => executeDarkModeToggle());
    };

    const executeDarkModeToggle = () => {
        setLayoutConfig(prev => ({ ...prev, darkTheme: !prev.darkTheme }));
        document.documentElement.classList.toggle('app-dark');
    };

    const toggleMenu = () => {
        if (layoutConfig.menuMode === 'overlay') {
            setLayoutState(prev => ({
                ...prev,
                overlayMenuActive: !prev.overlayMenuActive
            }));
        }

        if (window.innerWidth > 991) {
            setLayoutState(prev => {
                const newInactive = !prev.staticMenuDesktopInactive;
                Cookies.set('menuState', newInactive);
                return { ...prev, staticMenuDesktopInactive: newInactive };
            });
        } else {
            setLayoutState(prev => ({
                ...prev,
                staticMenuMobileActive: !prev.staticMenuMobileActive
            }));
        }
    };

    const value = useMemo(() => ({
        layoutConfig,
        layoutState,
        setLayoutConfig,
        setLayoutState,
        toggleMenu,
        setActiveMenuItem,
        toggleDarkMode,
        isSidebarActive: layoutState.overlayMenuActive || layoutState.staticMenuMobileActive,
        isDarkTheme: layoutConfig.darkTheme,
        getPrimary: layoutConfig.primary,
        getSurface: layoutConfig.surface
    }), [layoutConfig, layoutState]);

    return (
        <LayoutContext.Provider value={value}>
            {children}
        </LayoutContext.Provider>
    );
};

export const useLayout = () => {
    const context = useContext(LayoutContext);
    if (!context) {
        throw new Error('useLayout must be used within LayoutProvider');
    }
    return context;
};

