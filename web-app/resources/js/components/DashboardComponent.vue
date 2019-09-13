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
                    <div class="col">
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
                                <p v-show="!new_user">{{ getUser.email }}<br/>
                                    {{ getUser.address_1 }}<br/>
                                    {{ getUser.city }}, <span v-if="mounted">{{ getUser.region.id }}</span> {{ getUser.postal_code }}
                                </p>
                                <p v-if="getUser.professional_certificate_bc">
                                    <strong>BC Professional Certificate:</strong> {{ getUser.professional_certificate_bc }}
                                </p>
                                <p v-if="getUser.professional_certificate_yk">
                                    <strong>Yukon Professional Certificate:</strong> {{ getUser.professional_certificate_yk }}</p>
                                <p v-if="getUser.district">
                                    <strong>District:</strong> {{ getUser.district.name }}</p>
                                <p v-if="getUser.school">
                                    <strong>School:</strong> {{ getUser.school.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
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
                                    <div class="col">{{ credential.credential.name }}</div>
                                </div>
                                <div class="row pt-3">
                                    <div class="col">
                                        <select v-model="new_credential">
                                            <option value="0">Select New Credential</option>
                                            <option v-for="credential in credentialsAvailable" :value="credential">
                                                {{ credential.name }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col">
                                        <button class="btn btn-primary btn-sm" @click="addCredential(new_credential)"
                                                :disabled="disableAddCredentialButton">
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
                <div class="row pt-3">
                    <div class="col">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="row">
                                    <div class="col">
                                        <h2>Marking Sessions</h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        Click on a session below to apply, accept or decline
                                    </div>
                                    <div class="col-6">

                                        <ul class="nav nav-tabs justify-content-end pt-2">
                                            <li class="nav-item mb-0">
                                                <a href="#"
                                                   @click="filter = ''"
                                                   class="nav-link"
                                                   :class="{ 'active': filter === '' }">All
                                                    <span class="badge badge-pill badge-primary">{{ getSessions.length }}</span></a>
                                            </li>
                                            <li class="nav-item mb-0">
                                                <a href="#"
                                                   @click="filter = 'Applied'"
                                                   class="nav-link"
                                                   :class="{ 'active': filter === 'Applied' }">Applied
                                                    <span class="badge badge-pill badge-primary">{{ countStatus('Applied') }}</span></a>
                                            </li>
                                            <li class="nav-item mb-0">
                                                <a href="#"
                                                   @click="filter = 'Invited'"
                                                   class="nav-link"
                                                   :class="{ 'active': filter === 'Invited' }">Invited
                                                    <span class="badge badge-pill badge-primary">{{ countStatus('Invited') }}</span></a>
                                            </li>
                                            <li class="nav-item mb-0">
                                                <a href="#"
                                                   @click="filter = 'Scheduled'"
                                                   class="nav-link"
                                                   :class="{ 'active': filter === 'Scheduled' }">Going
                                                    <span class="badge badge-pill badge-primary">{{ countStatus('Scheduled') }}</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="float-left">


                                </div>



                            </div>
                            <div class="card-body">
                                <table class="table table-hover">
                                    <tr>
                                        <th>Type</th>
                                        <th>Activity</th>
                                        <th>Dates</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                    </tr>
                                    <tbody>
                                    <tr @click="viewSession(session)"
                                        v-for="session in filteredSessions">
                                        <td>{{ session.type.name }}</td>
                                        <td>{{ session.activity.name }}</td>
                                        <td nowrap>{{ session.date }}</td>
                                        <td>{{ session.location }}</td>
                                        <td>{{ sessionStatus(session) }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal name="session_form" height="auto">
            <session :session="current_session"></session>
        </modal>
        <modal name="profile_form" height="auto" :scrollable="true" :clickToClose="false">
            <profile
                    :user="user"
                    :schools="schools"
                    :regions="regions"
                    :districts="districts"
                    :new_user="new_user"
                    dusk="profile-component"
            ></profile>
        </modal>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "DashboardComponent",
        props: {
            user: {},
            credentials: {},
            user_credentials: {},
            sessions: {},
            subjects: {},
            schools: {},
            regions: {},
            districts: {}
        },
        data() {
            return {
                credentials_applied: [...this.user_credentials],
                new_credential: 0,
                filter: '',
                current_session: {},
                new_user: false,
                working: false,
                mounted: false
            }
        },
        mounted() {
            console.log('Dashboard Mounted')

            this.$store.commit('SET_USER', this.user);
            this.$store.commit('SET_SESSIONS', this.sessions);

            Event.listen('credential-added', this.pushCredential);
            Event.listen('credential-deleted', this.removeCredential);
            Event.listen('profile-updated', this.updateProfile);
            Event.listen('session_status_updated', this.updateSessionStatus);

            if ( ! this.user.id) {
                this.new_user = true;
                this.showProfile()
            }

            this.mounted = true;
        },
        computed: {
            ...mapGetters([
                'getUser',
                'getSessions',
                'filterSessions'
            ]),
            filteredSessions() {
                var dashboard = this
                return this.getSessions.filter(function (session) {
                    if (dashboard.filter.length === 0) {
                        return true
                    }
                    return session.status.name === dashboard.filter
                })
            },
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

            },

            disableAddCredentialButton() {
                return this.new_credential === "0" || this.new_credential === 0;
            }

        },
        methods: {
            addCredential(selection) {
                console.log('adding credential', selection);

                this.working = true;

                var form = this;

                axios.post('/api/' + form.getUser.id + '/profile-credentials', {
                    credential_id: form.new_credential.id
                })
                    .then(function (response) {
                        form.working = false;
                        Event.fire('credential-added', response.data);
                        console.log('Create Success!', response.data)
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

            countStatus(status) {
                // var status
                return Object.values(this.getSessions).filter(function (assignment) {
                    return assignment.status.name === status
                }).length
            },
            pushCredential(profile_credential) {
                console.log('pushing credential', profile_credential.data.credential.id );

                // Add to the applied list
                this.credentials_applied.push(profile_credential.data);

                this.new_credential = 0;
            },
            removeCredential(profile_credential) {
                console.log('removeCredential', profile_credential);

                // Get the credential
                let index = this.credentials_applied.findIndex(credential => credential.credential.id === profile_credential);

                console.log('get the credential', index);

                let credential = this.credentials_applied[index].credential;

                // Remove the credential from the applied list
                this.credentials_applied.splice(index, 1);


                this.new_credential = 0;
            },

            sessionStatus(session) {
                switch (session.status.name) {
                    case 'Applied':
                        return "You've Applied"
                    case 'Invited':
                        return 'Accept Invitation!'
                    case 'Accepted':
                        return 'Contract Pending'
                    case 'Contract':
                        return 'Contract Pending'
                    case 'Confirmed':
                        return "You're Going!"
                    case 'Declined':
                        return 'Declined'
                    case 'Withdrew':
                        return 'Withdrew'
                    case 'Completed':
                        return 'Closed'
                }

                return 'Open'
            },
            viewSession(session) {
                console.log('View Session')
                this.current_session = session
                this.$modal.show('session_form');
            },
            closeModal() {
                this.$modal.hide('session_form');
            },
            showProfile() {
                this.$modal.show('profile_form');
            },
            updateProfile(user) {
                // We must have a valid user now
                console.log('updateProfile event', user.data);
                this.new_user = false;
                this.$store.commit('SET_USER', user.data)
            },
            updateSessionStatus(response) {
                console.log('updateSessionStatus', response);
                this.$store.commit('UPDATE_SESSION_STATUS', response);
            }
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
