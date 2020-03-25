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
                                <button @click="showProfile" class="float-right btn btn-primary">Edit</button>
                                <h2>
                                    <span v-if="getUser.preferred_first_name">{{ getUser.preferred_first_name }}</span>
                                    <span v-else>{{ getUser.first_name }}</span>
                                    {{ getUser.last_name }}
                                </h2>
                            </div>
                            <div class="card-body">
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
                        <div class="card">
                            <div class="card-header">
                                <h2>Credentials</h2>
                            </div>
                            <div class="card-body">
                                <div class="row" v-for="credential in credentials_applied">
                                    <div class="col-1 text-center">
                                        <font-awesome-icon v-if="credential.verified" icon="check" alt="verified"/>
                                        <font-awesome-icon v-else icon="trash" @click="deleteCredential(credential)"
                                                           alt="delete" style="color: red;"/>
                                    </div>
                                    <div class="col-11">{{ credential.credential.name }}
                                        <span v-if="credential.year" class="col">Year: {{ credential.year }}</span>
                                    </div>
                                </div>
                                <div class="row pt-3 pl-4">
                                    <div class="form-row">
                                        <div class="form-group col-sm-5">
                                            <label for="new_credential_year">Credential</label>
                                            <select v-model="new_credential.credential" class="form-control form-control-sm">
                                                <option value="0">Select New Credential</option>
                                                <option v-for="credential in credentialsAvailable" :value="credential">
                                                    {{ credential.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="new_credential_year">Year certified</label>
                                            <input v-model="new_credential.year" type="text" class="form-control form-control-sm" name="new_credential_year"
                                                   id="new_credential_year" placeholder="YYYY">

                                        </div>
                                        <button class="btn btn-primary btn-sm mt-4 mb-3" @click="addCredential()">
                                            <span>
                                                <div class="loader text-center" v-show="working"></div>
                                            </span>
                                            <div v-show="!working">Add</div>
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
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
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import MarkingSessions from './MarkingSessions.vue';

    export default {
        name: "DashboardComponent",

        components: {
            MarkingSessions
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
                credentials_applied: [...this.user_credentials],
                filter: '',
                current_session: {},
                new_user: false,
                working: false,
                mounted: false,
                blank_credential: {
                    credential: 0,
                    year: null
                },
                new_credential: {
                    credential: 0
                },
            }
        },
        mounted() {
            console.log('Dashboard Mounted')

            this.$store.commit('SET_USER', this.user);
            this.$store.commit('SET_SESSIONS', this.sessions);

            Event.listen('credential-added', this.pushCredential);
            Event.listen('credential-deleted', this.removeCredential);
            Event.listen('profile-updated', this.updateProfile);
            Event.listen('launch-profile-modal', this.showProfile);

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

            credentialsIdsInUse() {

                var arrayOfCredentialIds = [];

                this.credentials_applied.forEach( function (applied) {
                    arrayOfCredentialIds.push(applied.credential.id);
                });

                return arrayOfCredentialIds;
            },

            credentialsAvailable() {
                // subtract applied_credentials from credentials
                return this.credentials.filter(x => ! this.credentialsIdsInUse.includes(x.id));

            }

        },
        methods: {
            addCredential() {
                console.log('adding credential', this.new_credential);

                this.working = true;

                var form = this;

                console.log('form', form );

                axios.post('/api/' + form.getUser.id + '/profile-credentials', {
                    credential_id: this.new_credential.credential.id,
                    year: this.new_credential.year
                })
                    .then(function (response) {
                        form.working = false;
                        Event.fire('credential-added', response.data);
                        console.log('Create Success!', response.data);
                    })
                    .catch(function (error) {
                        form.working = false;
                        console.log('Failure!', error)
                    });
            },
            deleteCredential(profile_credential) {
                console.log('removing credential');

                this.working = true;

                var form = this;

                axios.delete('/api/' + form.getUser.id + '/profile-credentials/' + profile_credential.id )
                    .then(function (response) {
                        form.working = false;
                        Event.fire('credential-deleted', profile_credential.credential.id);
                        console.log('Delete Success!', profile_credential.credential.id )
                    })
                    .catch(function (error) {
                        form.working = false;
                        console.log('Failure!')
                    });
            },


            pushCredential(profile_credential) {
                console.log('pushing credential', profile_credential.data.credential.id );

                // Add to the applied list
                this.credentials_applied.push(profile_credential.data);

                this.new_credential = {
                    credential: 0,
                    year: null
                }
            },
            removeCredential(profile_credential) {
                console.log('removeCredential', profile_credential);

                // Get the credential
                let index = this.credentials_applied.findIndex(credential => credential.credential.id === profile_credential);

                console.log('get the credential', index);

                let credential = this.credentials_applied[index].credential;

                // Remove the credential from the applied list
                this.credentials_applied.splice(index, 1);


                //this.new_credential = this.blank_credential;
            },



            showProfile() {
                this.$modal.show('profile_form');
            },
            updateProfile(user) {
                // We must have a valid user now
                console.log('updateProfile event', user.data.data);
                this.new_user = false;
                this.$store.commit('SET_USER', user.data.data)
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
    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
