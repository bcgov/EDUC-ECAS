
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.Vuex = require('vuex');

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

Vue.use(Vuex);
Vue.use(VModal, { dynamic: true, injectModalsContainer: true });

Vue.component('dashboard', require('./components/DashboardComponent.vue').default);

// Vue.component('profile-component'       , require('./components/ProfileComponent.vue').default);
// Vue.component('profile-credentials'     , require('./components/ProfileCredentials.vue').default);
// Vue.component('marking-sessions'        , require('./components/MarkingSessions.vue').default);
                                            
Vue.component('session'                 , require('./components/Session.vue').default);
Vue.component('profile'                 , require('./components/Profile.vue').default);

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
};

const store = new Vuex.Store({
    state: {
        user: {},
        sessions: []
    },
    mutations: {
        'SET_USER' (state, user) {
            state.user = user;
        },
        'SET_SESSIONS' (state, sessions) {
            state.sessions = sessions;
        },
        'UPDATE_SESSION_STATUS' (state, response) {
            const index = state.sessions.findIndex(session => session.id === response.session_id)
            let session = state.sessions[index]
            session.status = response.status
            Vue.set(state.sessions, index, session)
        }
    },
    getters: {
        getUser: (state) => {
            return state.user
        },
        getSessions: (state) => {
            return state.sessions
        }
        // filterSessions: state  => {
        //     return state.sessions.filter(function(session) {
        //         // if (filter_term.length == 0) {
        //         //     return true
        //         // }
        //         return session.status == 'Applied'
        //     })
        // }
        // salesAccounts: state => {
        //     return state.accounts.filter(function(account) {
        //         return account.category == 'Sales'
        //     })
        // }
    }
});

import { library } from '@fortawesome/fontawesome-svg-core'
import { faTrash } from '@fortawesome/free-solid-svg-icons'
import { faCheck } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add(faCheck)
library.add(faTrash)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.config.productionTip = false;

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    store
});