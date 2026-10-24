import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // Follow the in-app theme toggle (AppConfigurator adds .app-dark to <html>), not the OS setting
    darkMode: ['selector', '.app-dark'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './Modules/**/resources/views/**/*.blade.php',
        './Modules/**/resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                // Figtree for Latin; Noto fills in Bangla, Arabic and Devanagari glyphs
                sans: ['Figtree', 'Noto Sans Bengali', 'Noto Sans Arabic', 'Noto Sans Devanagari', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
