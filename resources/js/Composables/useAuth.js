import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export default function useAuth() {
    const page = usePage();

    const user = computed(() => page.props.auth?.user ?? null);
    const roles = computed(() => page.props.auth?.roles ?? []);
    const permissions = computed(() => page.props.auth?.permissions ?? []);

    const can = (permission) => permissions.value.includes(permission);
    // 🔐 Permission checks
    const hasPermission = (permission) => {
        if (!permission) return true;
        return permissions.value.includes(permission);
    };

    const hasAnyPermission = (required = []) => {
        if (!required.length) return true;
        if (!Array.isArray(required)) required = [required];

        return required.some((p) => permissions.value.includes(p));
    };

    const hasAllPermissions = (required = []) => {
        if (!required.length) return true;
        if (!Array.isArray(required)) required = [required];

        return required.every((p) => permissions.value.includes(p));
    };

    // 👤 Role checks
    const hasRole = (role) => {
        if (!role) return true;
        return roles.value.includes(role);
    };

    const hasAnyRole = (required = []) => {
        if (!required.length) return true;
        if (!Array.isArray(required)) required = [required];

        return required.some((r) => roles.value.includes(r));
    };

    return {
        user,
        roles,
        permissions,
        can,

        // permissions
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,

        // roles
        hasRole,
        hasAnyRole,
    };
}
