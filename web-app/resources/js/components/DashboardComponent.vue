<template>
    <div>
        <div class="card">
            <div class="card-header"><h1>Dashboard</h1></div>
            <div class="card-body">
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
                                    {{ getUser.city }}, {{ getUser.region }}</p>
                                <p v-if="getUser.professional_certificate_bc">
                                    <strong>BC Professional Certificate:</strong> {{ getUser.professional_certificate_bc }}
                                </p>
                                <p v-if="getUser.professional_certificate_yk">
                                    <strong>Yukon Professional Certificate:</strong> {{ getUser.professional_certificate_yk }}</p>
                                <p v-if="getUser.professional_certificate_other">
                                    <strong>Other Certificate:</strong> {{ getUser.professional_certificate_other }}</p>
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
                                    <div class="col-1" style="color: red;" @click="deleteCredential(credential)"><font-awesome-icon icon="trash" /></div>
                                    <div class="col">{{ credential.name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-1">
                                        <button class="btn btn-primary btn-sm" @click="addCredential">+</button>
                                    </div>
                                    <div class="col">
                                        <select v-model="new_credential">
                                            <option value="0">Select New Credential</option>
                                            <option v-for="credential in credentials_available" :value="credential.id">
                                                {{ credential.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="float-left">Marking Sessions</h2>
                                <ul class="nav nav-tabs justify-content-end">
                                    <li class="nav-item">
                                        <a href="#"
                                           @click="filter = ''"
                                           class="nav-link"
                                           :class="{ 'active': filter == '' }">All
                                            <span class="badge badge-pill badge-primary">{{ sessions_local.length }}</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#"
                                           @click="filter = 'Applied'"
                                           class="nav-link"
                                           :class="{ 'active': filter == 'Applied' }">Applied
                                            <span class="badge badge-pill badge-primary">{{ countStatus('Applied') }}</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#"
                                           @click="filter = 'Invited'"
                                           class="nav-link"
                                           :class="{ 'active': filter == 'Invited' }">Invited
                                            <span class="badge badge-pill badge-primary">{{ countStatus('Invited') }}</span></a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#"
                                           @click="filter = 'Scheduled'"
                                           class="nav-link"
                                           :class="{ 'active': filter == 'Scheduled' }">Going
                                            <span class="badge badge-pill badge-primary">{{ countStatus('Scheduled') }}</span></a>
                                    </li>
                                </ul>
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
                                            v-for="session in filteredSessions(sessions_local)">
                                            <td>{{ session.type }}</td>
                                            <td>{{ session.activity }}</td>
                                            <td nowrap>{{ session.dates }}</td>
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
                    :user="getUser"
                    :schools="schools"
                    :regions="regions"
                    :districts="districts"
                    :payments="payments"
                    :new_user="new_user"
                    dusk="profile-component"
            ></profile>
        </modal>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: "Dashboard",
        props: {
            user: {},
            credentials: {},
            user_credentials: {},
            sessions: {},
            subjects: {},
            schools: {},
            regions: {},
            districts: {},
            payments: {}
        },
        data() {
            return {
                sessions_local: this.sessions,
                credentials_applied: [...this.user_credentials],
                credentials_available: [...this.credentials],
                new_credential: 0,
                filter: '',
                current_session: {},
                new_user: false
            }
        },
        mounted() {
            console.log('Dashboard Mounted')
            this.$store.commit('SET_USER', this.user)
            Event.listen('credential-added', this.pushCredential)
            Event.listen('credential-deleted', this.removeCredential)
            Event.listen('profile-updated', this.updateProfile)

            if (this.getUser.id === undefined) {
                this.new_user = true
                this.showProfile()
            }
        },
        computed: {
            ...mapGetters([
                'getUser'
                ])
        },
        methods: {
            addCredential() {
                console.log('adding credential')

                var form = this

                axios.post('/Dashboard/credential', {
                    credential_id: form.new_credential,
                    user_id: form.getUser.id
                })
                    .then(function (response) {
                        Event.fire('credential-added', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                    });
            },
            deleteCredential(profile_credential) {
                console.log('removing credential')

                var form = this

                axios.post('/Dashboard/credential/delete', {
                    profile_credential_id: profile_credential.id
                })
                    .then(function (response) {
                        Event.fire('credential-deleted', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                    });
            },
            countStatus(status) {
                // var status
                return Object.values(this.sessions_local).filter(function (assignment) {
                    return assignment.status == status
                }).length
            },
            pushCredential(profile_credential) {
                console.log('pushing credential')

                // Get the credential
                let index = this.credentials_available.findIndex(elm => elm.id === profile_credential.credential_id)
                let credential = this.credentials_available[index]
                credential.credential_id = credential.id
                credential.id = profile_credential.id

                // Remove the credential from the available list
                this.credentials_available.splice(index, 1)

                // Add to the applied list
                this.credentials_applied.unshift(credential)

                this.new_credential = 0;
            },
            removeCredential(profile_credential) {
                console.log('remove credential')

                // Get the credential
                let index = this.credentials_applied.findIndex(elm => elm.id === profile_credential.id)
                let credential = this.credentials_applied[index]

                // Remove the credential from the applied list
                this.credentials_applied.splice(index, 1)

                // Add to the available list
                this.credentials_available.unshift(credential)

                this.new_credential = 0;
            },
            filteredSessions(sessions) {
                var dashboard = this

                return sessions.filter(function (session) {
                    if (dashboard.filter.length == 0) {
                        return true
                    }
                    return session.status == dashboard.filter
                })
            },
            isStatus: function (session, status) {
                return session.status == status
            },
            sessionStatus(session) {
                switch (session.status) {
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
                this.new_user = false
                this.$store.commit('SET_USER', user)
            }
        }

    }
</script>

<style>
</style>
