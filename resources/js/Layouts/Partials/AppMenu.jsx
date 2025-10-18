import React from 'react';
import AppMenuItem from './AppMenuItem';

const AppMenu = () => {
    const model = [
        {
            label: 'Products',
            items: [
                { label: 'Category', icon: 'pi pi-fw pi-id-card', to: route('categories.index') },
                { label: 'Sub-category', icon: 'pi pi-fw pi-id-card', to: route('sub-categories.index') },
                { label: 'Brand', icon: 'pi pi-fw pi-check-square', to: route('brands.index') },
                { label: 'Tag', icon: 'pi pi-fw pi-check-square', to: route('tags.index') },
                { label: 'Product', icon: 'pi pi-fw pi-check-square', to: route('products.index') },
                { label: 'Payment Method', icon: 'pi pi-fw pi-check-square', to: route('payment-methods.index') },
            ]
        },
        {
            label: 'User',
            items: [
                { label: 'Role', icon: 'pi pi-fw pi-id-card', to: route('roles.index') },
                { label: 'Permission', icon: 'pi pi-fw pi-id-card', to: route('permissions.index') },
                { label: 'User', icon: 'pi pi-fw pi-id-card', to: route('users.index') },
            ]
        },
        {
            label: 'Setup',
            items: [
                { label: 'Branches', icon: 'pi pi-fw pi-id-card', to: route('branches.index') },
            ]
        },
    ];

    return (
        <ul className="layout-menu">
            {model.map((item, i) => (
                <React.Fragment key={i}>
                    {!item.separator && <AppMenuItem item={item} index={i} />}
                    {item.separator && <li className="menu-separator"></li>}
                </React.Fragment>
            ))}
        </ul>
    );
};

export default AppMenu;

