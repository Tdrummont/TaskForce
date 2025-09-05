import './bootstrap'
import './trusted-types'
import '../css/app.css'

import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

const appName = document.getElementsByTagName('title')[0]?.innerText || 'Laravel'

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    // pega locale do Inertia (definido pelo middleware) ou do Blade
    const initialLocale =
      props?.initialPage?.props?.__LOCALE__ ??
      window.__LOCALE__ ??
      'pt'

    // vue-i18n v9 (se estiver usando)
    // if (i18n?.global?.locale) {
    //   i18n.global.locale.value = initialLocale
    // }
    document.documentElement.setAttribute('lang', initialLocale)

    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue) // já pega rotas do Ziggy
      // .use(i18n)     // se estiver usando

    // helper global p/ links: injeta locale atual automaticamente
    app.config.globalProperties.$lr = (name, params = {}, absolute, config) => {
      const locale = props?.initialPage?.props?.__LOCALE__ ?? 'pt'
      return route(name, { locale, ...params }, absolute, config)
    }

    app.mount(el)
  },
  progress: { color: '#4B5563' },
})
