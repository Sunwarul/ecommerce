import { router, usePage } from "@inertiajs/vue3";
import { useToast } from "primevue/usetoast";
import { onMounted, onUnmounted } from "vue";

/**
 * Toast server flash messages (`->with('success'|'error', ...)`).
 * AuthenticatedLayout already shows errors for every page (useFlashToast({ success: false, error: true })),
 * so pages call useFlashToast() to add success toasts only.
 */
// The same response can reach us twice (initial mount + router "success" event): dedupe briefly.
const recent = new Map();
function fresh(key) {
    const now = Date.now();
    if (recent.has(key) && now - recent.get(key) < 1500) return false;
    recent.set(key, now);
    return true;
}

export function useFlashToast({ success = true, error = false } = {}) {
    const toast = useToast();
    const page = usePage();

    const show = (flash) => {
        if (success && flash?.success && fresh("s:" + flash.success)) {
            toast.add({ severity: "success", summary: "Success", detail: flash.success, life: 3000 });
        }
        if (error && flash?.error && fresh("e:" + flash.error)) {
            toast.add({ severity: "error", summary: "Error", detail: flash.error, life: 5000 });
        }
    };

    let off = null;
    onMounted(() => {
        show(page.props.flash);
        off = router.on("success", (event) => show(event.detail.page.props.flash));
    });
    onUnmounted(() => off && off());
}
