import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';


export default defineConfig({
    // Adjust Vites dev server for DDEV
    // https://vitejs.dev/config/server-options.html
    server: {
        // respond to all hosts
        host: '0.0.0.0',
        strictPort: true,
        port: 51733,
        hmr: {
            // Force the Vite client to connect via SSL
            // This will also force a "https://" URL in the public/hot file
            protocol: 'wss',
            // The host where the Vite dev server can be accessed
            // This will also force this host to be written to the public/hot file
            host: `${process.env.DDEV_HOSTNAME}`
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Http/Livewire/**',
            ],
        }),
    ],
});
