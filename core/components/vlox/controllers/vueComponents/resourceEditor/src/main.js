import Vue from 'vue'
import App from './App.vue'
import store from './store'
import VuejsDialog from 'vuejs-dialog';
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';


Vue.config.productionTip = false
Vue.prototype.$restRoute = '/vlox/assets/components/vlox';

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
// Optionally install the BootstrapVue icon components plugin
Vue.use(IconsPlugin)
Vue.use(VuejsDialog);

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')
