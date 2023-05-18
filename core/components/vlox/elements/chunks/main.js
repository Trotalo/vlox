import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createI18n } from "vue-i18n"
import App from './App.vue'
import { Quasar, Dialog, Loading } from 'quasar'

import './assets/main.css'
import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import '@quasar/extras/material-icons-round/material-icons-round.css'
import '@quasar/extras/fontawesome-v6/fontawesome-v6.css'

import messages from "@intlify/unplugin-vue-i18n/messages";

// Import Quasar css
import 'quasar/src/css/index.sass'

const app = createApp(App)

const i18n = createI18n({
  legacy: false,
  globalInjection: true,
  locale: "es",
  fallbackLocale: "en",
  availableLocales: ["en", "es"],
  messages: messages,
})

app.provide('wsroute', '/assets/components/cronos/rest/index.php')
app.provide('assetsRoute', '/assets/components/cronos/')
app.provide('mapBoxKey' , 'pk.eyJ1IjoiY2FtaWNhc2U4MiIsImEiOiJja3lld3Z2eTcwZzlsMnFxa2t5eTd6d254In0.XEkd9yfN7-iEVR8FB-bUyQ')

app.use(createPinia())
app.use(Quasar, {
  plugins: {
      Dialog,
      Loading
  }, // import Quasar plugins and add here
})
app.use(i18n)

app.mount('#q-app')
