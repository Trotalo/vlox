import Vue from 'vue'
import App from "./App.vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";

// Import Bootstrap an BootstrapVue CSS files (order is important)
import "bootstrap/dist/css/bootstrap.css";
import "bootstrap-vue/dist/bootstrap-vue.css";

// IMports for vuejs-dialog
import VuejsDialog from "vuejs-dialog";
// include the default style
import "vuejs-dialog/dist/vuejs-dialog.min.css";
import VueAwesomeSwiper from "vue-awesome-swiper";

import VueCalendly from 'vue-calendly';
import Tawk from 'vue-tawk'


// Tell Vue to install the plugin.
Vue.use(VuejsDialog);
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.use(VueAwesomeSwiper);
Vue.use(VueCalendly);
Vue.use(Tawk, {
  tawkSrc: 'https://embed.tawk.to/5ea77dce69e9320caac7e615/default'
})
Vue.config.productionTip = false

new Vue({
  render: h => h(App)
}).$mount('#app')
