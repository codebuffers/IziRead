import axiosClient from "../axios";
import { createStore } from "vuex";
import state from './state';
import * as actions from './actions';
import * as mutations from './mutations';

const store = createStore({
  state,
  getters: {},
  actions: {
    ...actions,
    registerSeller({ commit }, user) {
      return axiosClient.post('/register-seller', user)
        .then(({ data }) => {
          commit('setUser', data.user);
          commit('setToken', data.token);
          return data;
        });
    },
    logout({ commit }) {
      return axiosClient.post('/logout')
        .then(() => {
          commit('setToken', null);
          commit('setUser', {});
        })
        .catch(error => {
          commit('setToken', null);
          commit('setUser', {});
          throw error;
        });
    },
  },
  mutations,
});

export default store;
