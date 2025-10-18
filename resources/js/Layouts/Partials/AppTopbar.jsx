import React, { useRef, useState } from 'react';
import { Link, router } from '@inertiajs/react';
import { Button } from 'primereact/button';
import { Menu } from 'primereact/menu';
import { Badge } from 'primereact/badge';
import { Avatar } from 'primereact/avatar';
import { useLayout } from './LayoutContext';
import ApplicationLogo from '@/Components/ApplicationLogo';

const AppTopbar = () => {
    const { toggleMenu } = useLayout();
    const menu = useRef(null);

    const items = [
        {
            label: 'Profile',
            items: [
                {
                    label: 'Settings',
                    icon: 'pi pi-cog',
                    command: () => {
                        // Add settings navigation
                    }
                },
                {
                    label: 'Messages',
                    icon: 'pi pi-inbox',
                    badge: 2
                },
                {
                    label: 'Logout',
                    icon: 'pi pi-sign-out',
                    command: () => {
                        router.post(route('logout'), {}, {
                            onSuccess: () => {
                                window.location = '/';
                            }
                        });
                    }
                }
            ]
        }
    ];

    const toggle = (event) => {
        menu.current.toggle(event);
    };

    const itemTemplate = (item) => {
        return (
            <a className="flex items-center p-menuitem-link">
                <span className={item.icon} />
                <span className="ml-2">{item.label}</span>
                {item.badge && <Badge value={item.badge} className="ml-auto" />}
                {item.shortcut && (
                    <span className="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">
                        {item.shortcut}
                    </span>
                )}
            </a>
        );
    };

    return (
        <div className="layout-topbar">
            <div className="layout-topbar-logo-container">
                <button className="layout-menu-button layout-topbar-action" onClick={toggleMenu}>
                    <i className="pi pi-bars"></i>
                </button>
                <Link href="/" className="layout-topbar-logo">
                    <span>
                        <ApplicationLogo />
                    </span>
                </Link>
            </div>

            <div className="layout-topbar-actions">
                <div className="layout-topbar-menu hidden lg:block">
                    <div className="layout-topbar-menu-content">
                        <button type="button" className="layout-topbar-action">
                            <i className="pi pi-inbox"></i>
                            <span>Messages</span>
                        </button>
                        <Button
                            type="button"
                            label="Profile"
                            icon="pi pi-user"
                            className="layout-topbar-action1"
                            outlined
                            onClick={toggle}
                            aria-haspopup
                            aria-controls="overlay_menu"
                        />
                        <Menu
                            model={items}
                            className="w-full md:w-60"
                            id="overlay_menu"
                            popup
                            ref={menu}
                        >
                            <div className="p-menu-end">
                                <button className="relative overflow-hidden w-full border-0 bg-transparent flex items-start p-2 pl-4 hover:bg-surface-100 dark:hover:bg-surface-800 rounded-none cursor-pointer transition-colors duration-200">
                                    <Avatar
                                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                                        className="mr-2"
                                        shape="circle"
                                    />
                                    <span className="inline-flex flex-col items-start">
                                        <span className="font-bold">Amy Elsner</span>
                                        <span className="text-sm">Admin</span>
                                    </span>
                                </button>
                            </div>
                        </Menu>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default AppTopbar;

