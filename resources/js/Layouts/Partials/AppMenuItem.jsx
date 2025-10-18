import React, { useState, useEffect } from 'react';
import { Link, router } from '@inertiajs/react';
import { useLayout } from './LayoutContext';

const AppMenuItem = ({ item, index, root = true, parentItemKey = null }) => {
    const { layoutState, setActiveMenuItem, toggleMenu } = useLayout();
    const [isActiveMenu, setIsActiveMenu] = useState(false);
    const [itemKey, setItemKey] = useState(null);

    useEffect(() => {
        const key = parentItemKey ? `${parentItemKey}-${index}` : String(index);
        setItemKey(key);

        const activeItem = layoutState.activeMenuItem;
        setIsActiveMenu(
            activeItem === key || (activeItem ? activeItem.startsWith(key + '-') : false)
        );
    }, [parentItemKey, index]);

    useEffect(() => {
        const activeItem = layoutState.activeMenuItem;
        if (itemKey) {
            setIsActiveMenu(
                activeItem === itemKey || (activeItem ? activeItem.startsWith(itemKey + '-') : false)
            );
        }
    }, [layoutState.activeMenuItem, itemKey]);

    const itemClick = (event, item) => {
        if (item.disabled) {
            event.preventDefault();
            return;
        }

        if ((item.to || item.url) && (layoutState.staticMenuMobileActive || layoutState.overlayMenuActive)) {
            toggleMenu();
        }

        if (item.command) {
            item.command({ originalEvent: event, item: item });
        }

        const foundItemKey = item.items ? (isActiveMenu ? parentItemKey : itemKey) : itemKey;
        setActiveMenuItem(foundItemKey);
    };

    const checkActiveRoute = (item) => {
        return route().current() === item.to;
    };

    if (item.visible === false) return null;

    return (
        <li className={`${root ? 'layout-root-menuitem' : ''} ${isActiveMenu ? 'active-menuitem' : ''}`}>
            {root && item.visible !== false && (
                <div className="layout-menuitem-root-text">{item.label}</div>
            )}

            {(!item.to || item.items) && item.visible !== false && (
                <a
                    href={item.url}
                    onClick={(e) => itemClick(e, item)}
                    className={item.class}
                    target={item.target}
                    tabIndex="0"
                >
                    <i className={`${item.icon} layout-menuitem-icon`}></i>
                    <span className="layout-menuitem-text">{item.label}</span>
                    {item.items && <i className="pi pi-fw pi-angle-down layout-submenu-toggler"></i>}
                </a>
            )}

            {item.to && !item.items && item.visible !== false && (
                <Link
                    href={item.to}
                    onClick={(e) => itemClick(e, item)}
                    className={`${item.class || ''} ${checkActiveRoute(item) ? 'active-route' : ''}`}
                    tabIndex="0"
                >
                    <i className={`${item.icon} layout-menuitem-icon`}></i>
                    <span className="layout-menuitem-text">{item.label}</span>
                    {item.items && <i className="pi pi-fw pi-angle-down layout-submenu-toggler"></i>}
                </Link>
            )}

            {item.items && item.visible !== false && (
                <ul
                    className="layout-submenu"
                    style={{ display: root ? 'block' : (isActiveMenu ? 'block' : 'none') }}
                >
                    {item.items.map((child, i) => (
                        <AppMenuItem
                            key={i}
                            item={child}
                            index={i}
                            parentItemKey={itemKey}
                            root={false}
                        />
                    ))}
                </ul>
            )}
        </li>
    );
};

export default AppMenuItem;

