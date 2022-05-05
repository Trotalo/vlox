import Vue from 'vue'
import App from './App.vue'
import store from '@shared/store'
import VuejsDialog from 'vuejs-dialog';
import { BootstrapVue, BootstrapVueIcons } from 'bootstrap-vue'
import ace from 'ace-builds'
import '../../shared/globalConstants';
import { ValidationProvider } from 'vee-validate';
//import { required } from 'vee-validate/dist/rules';

import '../../shared/css/style.css'
import 'bootstrap-vue/dist/bootstrap-vue-icons.min.css';

// include the default style
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import 'vuejs-dialog/dist/vuejs-dialog.min.css';

/*extend('required', {
  ...required,
  message: 'This field is required'
});*/


Vue.config.productionTip = false

// Make BootstrapVue available throughout your project
Vue.use(BootstrapVue)
Vue.use(VuejsDialog);
Vue.use(ace);
Vue.use(BootstrapVueIcons);

new Vue({
  store,
  render: h => h(App),
  components: {
    'validation-provider': ValidationProvider
  },
}).$mount('#app')
