// vite.config.js
import { defineConfig } from "file:///home/tdrummont/Documentos/TaskForce/node_modules/vite/dist/node/index.js";
import laravel from "file:///home/tdrummont/Documentos/TaskForce/node_modules/laravel-vite-plugin/dist/index.js";
import vue from "file:///home/tdrummont/Documentos/TaskForce/node_modules/@vitejs/plugin-vue/dist/index.mjs";
import { resolve } from "path";
var __vite_injected_original_dirname = "/home/tdrummont/Documentos/TaskForce";
var vite_config_default = defineConfig({
  server: {
    host: "127.0.0.1",
    port: 5173,
    strictPort: true,
    proxy: {
      // Proxy para as rotas da API do Laravel
      "/api": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      "/sanctum": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      "/broadcasting": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      // Proxy para arquivos estáticos do Laravel
      "/storage": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      // Proxy para outras rotas do Laravel
      "/pt": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      "/en": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      "/es": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      },
      // Proxy para rotas de autenticação
      "/auth": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false
      }
    }
  },
  plugins: [
    laravel({
      input: "resources/js/app.js",
      refresh: true
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false
        }
      }
    })
  ],
  define: {
    __VUE_OPTIONS_API__: true,
    __VUE_PROD_DEVTOOLS__: false
  },
  resolve: {
    alias: {
      "@": resolve(__vite_injected_original_dirname, "resources/js")
    }
  },
  optimizeDeps: {
    include: ["laravel-echo", "pusher-js"]
  }
});
export {
  vite_config_default as default
};
//# sourceMappingURL=data:application/json;base64,ewogICJ2ZXJzaW9uIjogMywKICAic291cmNlcyI6IFsidml0ZS5jb25maWcuanMiXSwKICAic291cmNlc0NvbnRlbnQiOiBbImNvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9kaXJuYW1lID0gXCIvaG9tZS90ZHJ1bW1vbnQvRG9jdW1lbnRvcy9UYXNrRm9yY2VcIjtjb25zdCBfX3ZpdGVfaW5qZWN0ZWRfb3JpZ2luYWxfZmlsZW5hbWUgPSBcIi9ob21lL3RkcnVtbW9udC9Eb2N1bWVudG9zL1Rhc2tGb3JjZS92aXRlLmNvbmZpZy5qc1wiO2NvbnN0IF9fdml0ZV9pbmplY3RlZF9vcmlnaW5hbF9pbXBvcnRfbWV0YV91cmwgPSBcImZpbGU6Ly8vaG9tZS90ZHJ1bW1vbnQvRG9jdW1lbnRvcy9UYXNrRm9yY2Uvdml0ZS5jb25maWcuanNcIjtpbXBvcnQgeyBkZWZpbmVDb25maWcgfSBmcm9tICd2aXRlJztcbmltcG9ydCBsYXJhdmVsIGZyb20gJ2xhcmF2ZWwtdml0ZS1wbHVnaW4nO1xuaW1wb3J0IHZ1ZSBmcm9tICdAdml0ZWpzL3BsdWdpbi12dWUnO1xuaW1wb3J0IHsgcmVzb2x2ZSB9IGZyb20gJ3BhdGgnO1xuXG5leHBvcnQgZGVmYXVsdCBkZWZpbmVDb25maWcoe1xuICAgIHNlcnZlcjoge1xuICAgICAgICBob3N0OiAnMTI3LjAuMC4xJyxcbiAgICAgICAgcG9ydDogNTE3MyxcbiAgICAgICAgc3RyaWN0UG9ydDogdHJ1ZSxcbiAgICAgICAgcHJveHk6IHtcbiAgICAgICAgICAgIC8vIFByb3h5IHBhcmEgYXMgcm90YXMgZGEgQVBJIGRvIExhcmF2ZWxcbiAgICAgICAgICAgICcvYXBpJzogeyBcbiAgICAgICAgICAgICAgICB0YXJnZXQ6ICdodHRwOi8vMTI3LjAuMC4xOjgwMDInLCBcbiAgICAgICAgICAgICAgICBjaGFuZ2VPcmlnaW46IHRydWUsIFxuICAgICAgICAgICAgICAgIHNlY3VyZTogZmFsc2UgXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJy9zYW5jdHVtJzogeyBcbiAgICAgICAgICAgICAgICB0YXJnZXQ6ICdodHRwOi8vMTI3LjAuMC4xOjgwMDInLCBcbiAgICAgICAgICAgICAgICBjaGFuZ2VPcmlnaW46IHRydWUsIFxuICAgICAgICAgICAgICAgIHNlY3VyZTogZmFsc2UgXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJy9icm9hZGNhc3RpbmcnOiB7IFxuICAgICAgICAgICAgICAgIHRhcmdldDogJ2h0dHA6Ly8xMjcuMC4wLjE6ODAwMicsIFxuICAgICAgICAgICAgICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSwgXG4gICAgICAgICAgICAgICAgc2VjdXJlOiBmYWxzZSBcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAvLyBQcm94eSBwYXJhIGFycXVpdm9zIGVzdFx1MDBFMXRpY29zIGRvIExhcmF2ZWxcbiAgICAgICAgICAgICcvc3RvcmFnZSc6IHsgXG4gICAgICAgICAgICAgICAgdGFyZ2V0OiAnaHR0cDovLzEyNy4wLjAuMTo4MDAyJywgXG4gICAgICAgICAgICAgICAgY2hhbmdlT3JpZ2luOiB0cnVlLCBcbiAgICAgICAgICAgICAgICBzZWN1cmU6IGZhbHNlIFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIC8vIFByb3h5IHBhcmEgb3V0cmFzIHJvdGFzIGRvIExhcmF2ZWxcbiAgICAgICAgICAgICcvcHQnOiB7IFxuICAgICAgICAgICAgICAgIHRhcmdldDogJ2h0dHA6Ly8xMjcuMC4wLjE6ODAwMicsIFxuICAgICAgICAgICAgICAgIGNoYW5nZU9yaWdpbjogdHJ1ZSwgXG4gICAgICAgICAgICAgICAgc2VjdXJlOiBmYWxzZSBcbiAgICAgICAgICAgIH0sXG4gICAgICAgICAgICAnL2VuJzogeyBcbiAgICAgICAgICAgICAgICB0YXJnZXQ6ICdodHRwOi8vMTI3LjAuMC4xOjgwMDInLCBcbiAgICAgICAgICAgICAgICBjaGFuZ2VPcmlnaW46IHRydWUsIFxuICAgICAgICAgICAgICAgIHNlY3VyZTogZmFsc2UgXG4gICAgICAgICAgICB9LFxuICAgICAgICAgICAgJy9lcyc6IHsgXG4gICAgICAgICAgICAgICAgdGFyZ2V0OiAnaHR0cDovLzEyNy4wLjAuMTo4MDAyJywgXG4gICAgICAgICAgICAgICAgY2hhbmdlT3JpZ2luOiB0cnVlLCBcbiAgICAgICAgICAgICAgICBzZWN1cmU6IGZhbHNlIFxuICAgICAgICAgICAgfSxcbiAgICAgICAgICAgIC8vIFByb3h5IHBhcmEgcm90YXMgZGUgYXV0ZW50aWNhXHUwMEU3XHUwMEUzb1xuICAgICAgICAgICAgJy9hdXRoJzogeyBcbiAgICAgICAgICAgICAgICB0YXJnZXQ6ICdodHRwOi8vMTI3LjAuMC4xOjgwMDInLCBcbiAgICAgICAgICAgICAgICBjaGFuZ2VPcmlnaW46IHRydWUsIFxuICAgICAgICAgICAgICAgIHNlY3VyZTogZmFsc2UgXG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9LFxuICAgIHBsdWdpbnM6IFtcbiAgICAgICAgbGFyYXZlbCh7XG4gICAgICAgICAgICBpbnB1dDogJ3Jlc291cmNlcy9qcy9hcHAuanMnLFxuICAgICAgICAgICAgcmVmcmVzaDogdHJ1ZSxcbiAgICAgICAgfSksXG4gICAgICAgIHZ1ZSh7XG4gICAgICAgICAgICB0ZW1wbGF0ZToge1xuICAgICAgICAgICAgICAgIHRyYW5zZm9ybUFzc2V0VXJsczoge1xuICAgICAgICAgICAgICAgICAgICBiYXNlOiBudWxsLFxuICAgICAgICAgICAgICAgICAgICBpbmNsdWRlQWJzb2x1dGU6IGZhbHNlLFxuICAgICAgICAgICAgICAgIH0sXG4gICAgICAgICAgICB9LFxuICAgICAgICB9KSxcbiAgICBdLFxuICAgIGRlZmluZToge1xuICAgICAgICBfX1ZVRV9PUFRJT05TX0FQSV9fOiB0cnVlLFxuICAgICAgICBfX1ZVRV9QUk9EX0RFVlRPT0xTX186IGZhbHNlLFxuICAgIH0sXG4gICAgcmVzb2x2ZToge1xuICAgICAgICBhbGlhczoge1xuICAgICAgICAgICAgJ0AnOiByZXNvbHZlKF9fZGlybmFtZSwgJ3Jlc291cmNlcy9qcycpLFxuICAgICAgICB9LFxuICAgIH0sXG4gICAgb3B0aW1pemVEZXBzOiB7XG4gICAgICAgIGluY2x1ZGU6IFsnbGFyYXZlbC1lY2hvJywgJ3B1c2hlci1qcyddLFxuICAgIH0sXG59KTtcbiJdLAogICJtYXBwaW5ncyI6ICI7QUFBOFIsU0FBUyxvQkFBb0I7QUFDM1QsT0FBTyxhQUFhO0FBQ3BCLE9BQU8sU0FBUztBQUNoQixTQUFTLGVBQWU7QUFIeEIsSUFBTSxtQ0FBbUM7QUFLekMsSUFBTyxzQkFBUSxhQUFhO0FBQUEsRUFDeEIsUUFBUTtBQUFBLElBQ0osTUFBTTtBQUFBLElBQ04sTUFBTTtBQUFBLElBQ04sWUFBWTtBQUFBLElBQ1osT0FBTztBQUFBO0FBQUEsTUFFSCxRQUFRO0FBQUEsUUFDSixRQUFRO0FBQUEsUUFDUixjQUFjO0FBQUEsUUFDZCxRQUFRO0FBQUEsTUFDWjtBQUFBLE1BQ0EsWUFBWTtBQUFBLFFBQ1IsUUFBUTtBQUFBLFFBQ1IsY0FBYztBQUFBLFFBQ2QsUUFBUTtBQUFBLE1BQ1o7QUFBQSxNQUNBLGlCQUFpQjtBQUFBLFFBQ2IsUUFBUTtBQUFBLFFBQ1IsY0FBYztBQUFBLFFBQ2QsUUFBUTtBQUFBLE1BQ1o7QUFBQTtBQUFBLE1BRUEsWUFBWTtBQUFBLFFBQ1IsUUFBUTtBQUFBLFFBQ1IsY0FBYztBQUFBLFFBQ2QsUUFBUTtBQUFBLE1BQ1o7QUFBQTtBQUFBLE1BRUEsT0FBTztBQUFBLFFBQ0gsUUFBUTtBQUFBLFFBQ1IsY0FBYztBQUFBLFFBQ2QsUUFBUTtBQUFBLE1BQ1o7QUFBQSxNQUNBLE9BQU87QUFBQSxRQUNILFFBQVE7QUFBQSxRQUNSLGNBQWM7QUFBQSxRQUNkLFFBQVE7QUFBQSxNQUNaO0FBQUEsTUFDQSxPQUFPO0FBQUEsUUFDSCxRQUFRO0FBQUEsUUFDUixjQUFjO0FBQUEsUUFDZCxRQUFRO0FBQUEsTUFDWjtBQUFBO0FBQUEsTUFFQSxTQUFTO0FBQUEsUUFDTCxRQUFRO0FBQUEsUUFDUixjQUFjO0FBQUEsUUFDZCxRQUFRO0FBQUEsTUFDWjtBQUFBLElBQ0o7QUFBQSxFQUNKO0FBQUEsRUFDQSxTQUFTO0FBQUEsSUFDTCxRQUFRO0FBQUEsTUFDSixPQUFPO0FBQUEsTUFDUCxTQUFTO0FBQUEsSUFDYixDQUFDO0FBQUEsSUFDRCxJQUFJO0FBQUEsTUFDQSxVQUFVO0FBQUEsUUFDTixvQkFBb0I7QUFBQSxVQUNoQixNQUFNO0FBQUEsVUFDTixpQkFBaUI7QUFBQSxRQUNyQjtBQUFBLE1BQ0o7QUFBQSxJQUNKLENBQUM7QUFBQSxFQUNMO0FBQUEsRUFDQSxRQUFRO0FBQUEsSUFDSixxQkFBcUI7QUFBQSxJQUNyQix1QkFBdUI7QUFBQSxFQUMzQjtBQUFBLEVBQ0EsU0FBUztBQUFBLElBQ0wsT0FBTztBQUFBLE1BQ0gsS0FBSyxRQUFRLGtDQUFXLGNBQWM7QUFBQSxJQUMxQztBQUFBLEVBQ0o7QUFBQSxFQUNBLGNBQWM7QUFBQSxJQUNWLFNBQVMsQ0FBQyxnQkFBZ0IsV0FBVztBQUFBLEVBQ3pDO0FBQ0osQ0FBQzsiLAogICJuYW1lcyI6IFtdCn0K
