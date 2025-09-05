import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    server: {
        host: '127.0.0.1',
        port: 5173,
        strictPort: true,
        proxy: {
            // Proxy para as rotas da API do Laravel
            '/api': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            '/sanctum': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            '/broadcasting': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            // Proxy para arquivos estáticos do Laravel
            '/storage': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            // Proxy para outras rotas do Laravel
            '/pt': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            '/en': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            },
            '/es': { 
                target: 'http://127.0.0.1:8001', 
                changeOrigin: true, 
                secure: false 
            }
        }
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
    optimizeDeps: {
        include: ['laravel-echo', 'pusher-js'],
    },
});
