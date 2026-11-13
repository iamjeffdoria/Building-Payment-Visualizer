import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/login.js',
                'resources/js/pages/dashboard.js',
                'resources/js/pages/buildings-index.js',
                'resources/js/pages/buildings-show.js',
                'resources/js/pages/stall-show.js',
                'resources/js/pages/users.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
