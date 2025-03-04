import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// PrimeVue related imports
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost'
        },
        fs: {
            allow: ['node_modules', 'resources', 'vendor', 'Modules'],
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }), // primevue related starts
        Components({
            resolvers: [
                PrimeVueResolver()
            ]
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '@modules': '/Modules',
        },
    },
});
