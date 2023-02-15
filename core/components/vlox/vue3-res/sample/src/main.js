import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { Quasar } from 'quasar'

import './assets/main.css'
import '@quasar/extras/roboto-font/roboto-font.css'
import '@quasar/extras/material-icons/material-icons.css'
import '@quasar/extras/material-icons-outlined/material-icons-outlined.css'
import '@quasar/extras/material-icons-round/material-icons-round.css'
import '@quasar/extras/fontawesome-v6/fontawesome-v6.css'

// Import Quasar css
import 'quasar/src/css/index.sass'

const app = createApp(App)

app.use(createPinia())
app.use(Quasar, {
  plugins: {}, // import Quasar plugins and add here
})

app.mount('#app')
