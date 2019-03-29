
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Vuex = require('vuex')

import Vue from 'vue'
import Vuex from 'vuex'

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

import VModal from 'vue-js-modal'

Vue.use(Vuex)
Vue.use(VModal, { dynamic: true, injectModalsContainer: true })

Vue.component('dashboard', require('./components/DashboardComponent.vue').default);
Vue.component('session', require('./components/Session.vue').default);
Vue.component('profile', require('./components/Profile.vue').default);

// A global event handler, just a convenient wrapper for Vue's event system
window.Event = new class {
    constructor() {
        this.vue = new Vue();
    }
    fire(event, data = null) {
        this.vue.$emit(event, data)
    }
    listen(event, callback) {
        this.vue.$on(event, callback)
    }
}

const store = new Vuex.Store({
    state: {
        my_variable: {}
    },
    mutations: {
        'SET_VARIABLE' (state, value) {
            state.my_variable = value;
        }
    },
    getters: {
        account: (state) => (id) => {
            return state.accounts.find(function(account) {
                return account.id == id
            })
        }
    }
})

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store
});