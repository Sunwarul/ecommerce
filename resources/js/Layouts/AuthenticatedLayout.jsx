import React, { useState, useEffect, useRef } from 'react';
import { Toast } from 'primereact/toast';
import { LayoutProvider, useLayout } from '@/Layouts/Partials/LayoutContext';
import AppFooter from '@/Layouts/Partials/AppFooter';
import AppSidebar from '@/Layouts/Partials/AppSidebar';
import AppTopbar from '@/Layouts/Partials/AppTopbar';
import FavIcons from '@/Layouts/Partials/FavIcons';

const AuthenticatedLayoutContent = ({ children }) => {
    const { layoutConfig, layoutState, isSidebarActive } = useLayout();
    const outsideClickListener = useRef(null);

    useEffect(() => {
        if (isSidebarActive) {
            bindOutsideClickListener();
        } else {
            unbindOutsideClickListener();
        }

        return () => {
            unbindOutsideClickListener();
        };
    }, [isSidebarActive]);

    const bindOutsideClickListener = () => {
        if (!outsideClickListener.current) {
            outsideClickListener.current = (event) => {
                if (isOutsideClicked(event)) {
                    const { setLayoutState } = useLayout();
                    setLayoutState(prev => ({
                        ...prev,
                        overlayMenuActive: false,
                        staticMenuMobileActive: false,
                        menuHoverActive: false
                    }));
                }
            };
            document.addEventListener('click', outsideClickListener.current);
        }
    };

    const unbindOutsideClickListener = () => {
        if (outsideClickListener.current) {
            document.removeEventListener('click', outsideClickListener.current);
            outsideClickListener.current = null;
        }
    };

    const isOutsideClicked = (event) => {
        const sidebarEl = document.querySelector('.layout-sidebar');
        const topbarEl = document.querySelector('.layout-menu-button');

        return !(
            sidebarEl?.isSameNode(event.target) ||
            sidebarEl?.contains(event.target) ||
            topbarEl?.isSameNode(event.target) ||
            topbarEl?.contains(event.target)
        );
    };

    const containerClass = `layout-wrapper ${layoutConfig.menuMode === 'overlay' ? 'layout-overlay' : ''
        } ${layoutConfig.menuMode === 'static' ? 'layout-static' : ''} ${layoutState.staticMenuDesktopInactive && layoutConfig.menuMode === 'static'
            ? 'layout-static-inactive'
            : ''
        } ${layoutState.overlayMenuActive ? 'layout-overlay-active' : ''} ${layoutState.staticMenuMobileActive ? 'layout-mobile-active' : ''
        }`;

    return (
        <>
            <FavIcons />
            <div className={containerClass}>
                <AppTopbar />
                <AppSidebar />
                <div className="layout-main-container">
                    <div className="layout-main">
                        {children}
                    </div>
                    <AppFooter />
                </div>
                <div className="layout-mask animate-fadein"></div>
            </div>
            <Toast />
        </>
    );
};

const AuthenticatedLayout = ({ children }) => {
    return (
        <LayoutProvider>
            <AuthenticatedLayoutContent>
                {children}
            </AuthenticatedLayoutContent>
        </LayoutProvider>
    );
};

export default AuthenticatedLayout;

