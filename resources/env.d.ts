/// <reference types="vite/client" />

declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<{}, {}, any>
  export default component
}

// Ziggy (helper global `route()` registrado pelo ZiggyVue)
declare global {
  function route(
    name: string,
    params?: Record<string, any>,
    absolute?: boolean,
    config?: any
  ): string
}

export {}
