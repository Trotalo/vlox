import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    refreshList: true,
    restRoute: '/vlox/assets/components/vlox',
    showLoading: false
  },
  mutations: {
    change (state, refreshList) {
      state.refreshList = refreshList;
    },
    CHANGE_LOADING_STATUS(state, val){
      state.showLoading = val;
    }
  },
  actions: {
    showLoading({ commit }) {
      commit("CHANGE_LOADING_STATUS", true);
    },
    hideLoading({ commit }) {
      commit("CHANGE_LOADING_STATUS", false);
    },
  },
  getters: {
    refreshList (state) {
      return state.refreshList;
    },
    restRoute(state) {
      return state.restRoute;
    },
    showLoading(state) {
      return state.showLoading;
    }
  },
  modules: {
  }
})
