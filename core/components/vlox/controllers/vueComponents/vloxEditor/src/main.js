import Vue from 'vue'
import App from './App.vue'
import store from './store'
import VuejsDialog from 'vuejs-dialog';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import ace from 'ace-builds'
import '../../shared/globalConstants';

import '../../shared/css/style.css'

// include the default style
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vuejs-dialog/dist/vuejs-dialog.min.css';


Vue.config.productionTip = false

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)

Vue.use(VuejsDialog);
Vue.use(ace);





new Vue({
  store,
  render: h => h(App)
}).$mount('#app')
