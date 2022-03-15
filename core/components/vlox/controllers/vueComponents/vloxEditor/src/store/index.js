import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    refreshList: true,
    restRoute: '/vlox/assets/components/vlox',
  },
  mutations: {
    change (state, refreshList) {
      state.refreshList = refreshList;
    }
  },
  actions: {
  },
  getters: {
    refreshList (state) {
      return state.refreshList;
    },
    restRoute(state) {
      return state.restRoute;
    }
  },
  modules: {
  }
})
