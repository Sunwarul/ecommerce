import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

export default function useAuth() {
    const page = usePage();

    const user = computed(() => page.props.auth?.user ?? null);
    const roles = computed(() => page.props.auth?.roles ?? []);
    const permissions = computed(() => page.props.auth?.permissions ?? []);

    const can = (permission) => permissions.value.includes(permission);

    return {
        user,
        roles,
        permissions,
        can,
    };
}
