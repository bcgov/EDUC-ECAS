<template>
    <div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div id="logout">
                            <button type="button" class="btn btn-primary" @click="$keycloak.logoutFn" v-if="$keycloak.authenticated">Log out</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col pb-3">
                        <div class="card">
                            <div class="card-header">
                                <button @click="toggleMenu" :class="[menuVisible? 'btn-secondary' : 'btn-primary', 'float-right', 'btn']">Menu</button>
                                <h2>
                                    <span v-if="getUser.preferred_first_name">{{ getUser.preferred_first_name }}</span>
                                    <span v-else>{{ getUser.first_name }}</span>
                                    {{ getUser.last_name }}
                                </h2>
                            </div>
                            <div class="card-body">
                                <ul v-show="menuVisible" class="list-group mt-n3 float-right">
                                    <li class="list-group-item d-flex justify-content-between align-items-center"
                                        @click="showProfile">
                                        Profile
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"
                                        @click="showCredentials">
                                        Credentials
                                    </li>
                                </ul>
                                <p v-show="!new_user">
                                    {{ getUser.email }}<br/>
                                    {{ getUser.address_1 }}<br/>
                                    <span v-if="getUser.address_2">{{ getUser.address_2 }}<br /></span>
                                    {{ getUser.city }}, <span v-if="mounted">{{ getUser.region.id }}</span> {{ getUser.postal_code }}
                                </p>
                                <p v-if="getUser.professional_certificate_bc === 'Yes'">
                                    <strong>BC Professional Certificate:</strong>
                                    <font-awesome-icon icon="check" alt="BC Professional Certificate"/>
                                </p>
                                <p v-if="getUser.professional_certificate_yk === 'Yes'" >
                                    <strong>Yukon Professional Certificate:</strong>
                                    <font-awesome-icon icon="check" alt="Yukon Professional Certificate"/>
                                </p>
                                <p v-if="getUser.district">
                                    <strong>District:</strong> {{ getUser.district.name }}</p>
                                <p v-if="getUser.school">
                                    <strong>School:</strong> {{ getUser.school.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col pb-3">
                        <contracts dusk="contracts-component"></contracts>
                    </div>
                </div>
                <marking-sessions :sessions="this.getSessions"></marking-sessions>

            </div>
        </div>
        <modal name="profile_form" height="auto" :scrollable="true" :clickToClose="false">
            <profile
                    :user="getUser"
                    :regions="regions"
                    :countries="countries"
                    :new_user="new_user"
                    dusk="profile-component"
            ></profile>
        </modal>
        <modal name="credentials_form" height="auto" :scrollable="true" :clickToClose="true">
            <credentials
                    :user="getUser"
                    :user_credentials="user_credentials"
                    :credentials="credentials"
                    dusk="credentials-component"
            ></credentials>
        </modal>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import Profile from './Profile.vue';
    import Credentials from './Credentials.vue';
    import Contracts from './Contracts.vue';
    import MarkingSessions from './MarkingSessions.vue';

    export default {
        name: "DashboardComponent",

        components: {
            Profile,
            Credentials,
            Contracts,
            MarkingSessions,
        },

        props: {
            user: {},
            credentials: {},
            user_credentials: {},
            sessions: {},
            subjects: {},
            regions: {},
            countries: {},
        },
        data() {
            return {
                filter: '',
                current_session: {},
                new_user: false,
                mounted: false,
                menuVisible: false,
            }
        },
        mounted() {
            console.log('Dashboard Mounted')

            Event.listen('launch-profile-modal', this.showProfile);
            Event.listen('profile-updated', this.updateProfile);
            Event.listen('user-credentials-updated', this.updateUserCredentials);

            this.$store.commit('SET_USER', this.user);
            this.$store.commit('SET_SESSIONS', this.sessions);

            if ( ! this.user.id) {
                this.new_user = true;
                this.showProfile()
            }

            this.mounted = true;
        },
        computed: {
            ...mapGetters([
                'getUser',
                'getSessions'
            ]),
        },
        methods: {
            toggleMenu() {
                this.menuVisible = !this.menuVisible;
            },
            showProfile() {
                this.$modal.show('profile_form');
            },
            showCredentials() {
                this.$modal.show('credentials_form');
            },
            updateProfile(user) {
                // We must have a valid user now
                console.log('updateProfile event', user.data.data);
                this.new_user = false;
                this.$store.commit('SET_USER', user.data.data)
            },
            updateUserCredentials(credentials) {
                console.log('updateUserCredentials event', credentials);
                this.user_credentials = credentials;
            },
        }

    }
</script>

<style>
    .nav-tabs {
        border-bottom: none;
    }
    .loader {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 16px;
        height: 16px;
        margin: auto;
        animation: spin 2s linear infinite;
    }
    .list-group-item {
        padding: 0.35rem 0.65rem;
    }
    .list-group-item:hover {
        color: #003366;
        background-color: lightgray;
    }
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

