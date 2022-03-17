import Vue from 'vue'
import App from './App.vue'
import store from './store'
import VuejsDialog from 'vuejs-dialog';
import { BootstrapVue,  BootstrapVueIcons } from 'bootstrap-vue';
import '../../shared/globalConstants';

import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';

//import 'bootstrap-vue/src/icons.scss'
import 'bootstrap-vue/dist/bootstrap-vue-icons.min.css';


Vue.config.productionTip = false

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue);
// Optionally install the BootstrapVue icon components plugin
Vue.use(BootstrapVueIcons);
//Vue.use(IconsPlugin)
Vue.use(VuejsDialog);

new Vue({
  store,
  render: h => h(App)
}).$mount('#app')
