import { definePreset } from '@primevue/themes';
import Aura from '@primevue/themes/aura';
import Lara from '@primevue/themes/lara';

/*
 * Brand theme: emerald primary (matches the app's actions & accents), slate surfaces.
 * Red is reserved for danger states. Users can still pick another primary/surface in the
 * appearance panel (AppConfigurator); choices persist in localStorage and are applied on boot.
 */

export const PRESETS = { Aura, Lara };

export const PRIMARY_COLORS = {
    emerald: { 50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399', 500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22' },
    green: { 50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac', 400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d', 800: '#166534', 900: '#14532d', 950: '#052e16' },
    teal: { 50: '#f0fdfa', 100: '#ccfbf1', 200: '#99f6e4', 300: '#5eead4', 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488', 700: '#0f766e', 800: '#115e59', 900: '#134e4a', 950: '#042f2e' },
    cyan: { 50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9', 400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490', 800: '#155e75', 900: '#164e63', 950: '#083344' },
    sky: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' },
    blue: { 50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8', 800: '#1e40af', 900: '#1e3a8a', 950: '#172554' },
    indigo: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81', 950: '#1e1b4b' },
    violet: { 50: '#f5f3ff', 100: '#ede9fe', 200: '#ddd6fe', 300: '#c4b5fd', 400: '#a78bfa', 500: '#8b5cf6', 600: '#7c3aed', 700: '#6d28d9', 800: '#5b21b6', 900: '#4c1d95', 950: '#2e1065' },
    purple: { 50: '#faf5ff', 100: '#f3e8ff', 200: '#e9d5ff', 300: '#d8b4fe', 400: '#c084fc', 500: '#a855f7', 600: '#9333ea', 700: '#7e22ce', 800: '#6b21a8', 900: '#581c87', 950: '#3b0764' },
    orange: { 50: '#fff7ed', 100: '#ffedd5', 200: '#fed7aa', 300: '#fdba74', 400: '#fb923c', 500: '#f97316', 600: '#ea580c', 700: '#c2410c', 800: '#9a3412', 900: '#7c2d12', 950: '#431407' },
    amber: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309', 800: '#92400e', 900: '#78350f', 950: '#451a03' },
    rose: { 50: '#fff1f2', 100: '#ffe4e6', 200: '#fecdd3', 300: '#fda4af', 400: '#fb7185', 500: '#f43f5e', 600: '#e11d48', 700: '#be123c', 800: '#9f1239', 900: '#881337', 950: '#4c0519' },
};

export const SURFACES = {
    slate: { 0: '#ffffff', 50: '#f8fafc', 100: '#f1f5f9', 200: '#e2e8f0', 300: '#cbd5e1', 400: '#94a3b8', 500: '#64748b', 600: '#475569', 700: '#334155', 800: '#1e293b', 900: '#0f172a', 950: '#020617' },
    gray: { 0: '#ffffff', 50: '#f9fafb', 100: '#f3f4f6', 200: '#e5e7eb', 300: '#d1d5db', 400: '#9ca3af', 500: '#6b7280', 600: '#4b5563', 700: '#374151', 800: '#1f2937', 900: '#111827', 950: '#030712' },
    zinc: { 0: '#ffffff', 50: '#fafafa', 100: '#f4f4f5', 200: '#e4e4e7', 300: '#d4d4d8', 400: '#a1a1aa', 500: '#71717a', 600: '#52525b', 700: '#3f3f46', 800: '#27272a', 900: '#18181b', 950: '#09090b' },
    neutral: { 0: '#ffffff', 50: '#fafafa', 100: '#f5f5f5', 200: '#e5e5e5', 300: '#d4d4d4', 400: '#a3a3a3', 500: '#737373', 600: '#525252', 700: '#404040', 800: '#262626', 900: '#171717', 950: '#0a0a0a' },
    stone: { 0: '#ffffff', 50: '#fafaf9', 100: '#f5f5f4', 200: '#e7e5e4', 300: '#d6d3d1', 400: '#a8a29e', 500: '#78716c', 600: '#57534e', 700: '#44403c', 800: '#292524', 900: '#1c1917', 950: '#0c0a09' },
};

export const DEFAULT_THEME = {
    preset: 'Aura',
    primary: 'emerald',
    surface: 'slate',
    darkTheme: false,
    menuMode: 'static',
};

const STORAGE_KEY = 'app_theme';

export function loadThemeConfig() {
    try {
        const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || 'null');
        if (saved && typeof saved === 'object') {
            const cfg = { ...DEFAULT_THEME, ...saved };
            if (!PRESETS[cfg.preset]) cfg.preset = DEFAULT_THEME.preset;
            if (!PRIMARY_COLORS[cfg.primary]) cfg.primary = DEFAULT_THEME.primary;
            if (!SURFACES[cfg.surface]) cfg.surface = DEFAULT_THEME.surface;
            if (!['static', 'overlay'].includes(cfg.menuMode)) cfg.menuMode = DEFAULT_THEME.menuMode;
            return cfg;
        }
    } catch {
        // storage unavailable or corrupt: fall back to defaults
    }
    return { ...DEFAULT_THEME };
}

export function saveThemeConfig(cfg) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify({
            preset: cfg.preset,
            primary: cfg.primary,
            surface: cfg.surface,
            darkTheme: !!cfg.darkTheme,
            menuMode: cfg.menuMode,
        }));
    } catch {
        // ignore (private mode etc.)
    }
}

/** Semantic overrides for a primary palette (light + dark). */
export function primaryExtension(name) {
    return {
        semantic: {
            primary: PRIMARY_COLORS[name] || PRIMARY_COLORS.emerald,
            colorScheme: {
                light: {
                    primary: { color: '{primary.600}', contrastColor: '#ffffff', hoverColor: '{primary.700}', activeColor: '{primary.800}' },
                    highlight: { background: '{primary.50}', focusBackground: '{primary.100}', color: '{primary.700}', focusColor: '{primary.800}' },
                },
                dark: {
                    primary: { color: '{primary.400}', contrastColor: '{surface.900}', hoverColor: '{primary.300}', activeColor: '{primary.200}' },
                    highlight: {
                        background: 'color-mix(in srgb, {primary.400}, transparent 84%)',
                        focusBackground: 'color-mix(in srgb, {primary.400}, transparent 76%)',
                        color: 'rgba(255,255,255,.87)',
                        focusColor: 'rgba(255,255,255,.87)',
                    },
                },
            },
        },
    };
}

/** Full preset for a stored config (used at boot so the first paint is already themed). */
export function buildPreset(cfg = loadThemeConfig()) {
    const surface = SURFACES[cfg.surface] || SURFACES.slate;
    return definePreset(PRESETS[cfg.preset] || Aura, {
        ...primaryExtension(cfg.primary),
        semantic: {
            ...primaryExtension(cfg.primary).semantic,
            colorScheme: {
                light: { ...primaryExtension(cfg.primary).semantic.colorScheme.light, surface },
                dark: { ...primaryExtension(cfg.primary).semantic.colorScheme.dark, surface },
            },
        },
    });
}

/** Toggle the class Tailwind (darkMode selector), the dark-utility remap and PrimeVue all key off. */
export function applyDarkClass(dark) {
    document.documentElement.classList.toggle('app-dark', !!dark);
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', dark ? '#0f172a' : '#10b981');
}

const Theme = buildPreset();

export default Theme;
