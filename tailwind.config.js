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
                // The stack is a CSS variable (app.scss) so the fonts picked in Settings apply without a rebuild
                sans: ['var(--app-font-stack)', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
