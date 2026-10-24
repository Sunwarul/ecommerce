import { onBeforeUnmount, onMounted, ref } from "vue";

/**
 * Reactive view of the document theme and direction.
 * `isDark` follows the .app-dark class set by the theme toggle; `isRtl` follows <html dir>.
 * Read `themeKey.value` inside chart computeds so options/colours rebuild when either changes.
 */
export function useDarkMode() {
    const read = () => ({
        dark: document.documentElement.classList.contains("app-dark"),
        rtl: document.documentElement.dir === "rtl",
    });
    const initial = read();
    const isDark = ref(initial.dark);
    const isRtl = ref(initial.rtl);
    const themeKey = ref(0);

    let observer = null;
    onMounted(() => {
        observer = new MutationObserver(() => {
            const now = read();
            if (now.dark !== isDark.value || now.rtl !== isRtl.value) {
                isDark.value = now.dark;
                isRtl.value = now.rtl;
                themeKey.value++;
            }
        });
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ["class", "dir"] });
    });
    onBeforeUnmount(() => observer && observer.disconnect());

    return { isDark, isRtl, themeKey };
}

/** Apply RTL to Chart.js options: mirrored legend/tooltip and reversed category axis. */
export function withRtl(options, rtl) {
    if (!rtl) return options;
    const o = { ...options, plugins: { ...(options.plugins || {}) }, scales: { ...(options.scales || {}) } };
    if (o.plugins.legend) {
        o.plugins.legend = { ...o.plugins.legend, rtl: true, align: o.plugins.legend.align === "end" ? "start" : o.plugins.legend.align };
        if (o.plugins.legend.position === "right") o.plugins.legend.position = "left";
    }
    o.plugins.tooltip = { ...(o.plugins.tooltip || {}), rtl: true };
    // x runs right-to-left; the y axis sits on the right edge
    if (o.scales.x) o.scales.x = { ...o.scales.x, reverse: true };
    if (o.scales.y) o.scales.y = { ...o.scales.y, position: "right" };
    return o;
}
