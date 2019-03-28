<template>
    <div>
        <div class="card">
            <div class="card-header"><h1>Dashboard</h1></div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <a href="/Profile/edit" class="float-right btn btn-primary">Edit</a>
                                <h2>{{ user.preferred_first_name }} {{ user.last_name }}</h2>
                            </div>
                            <div class="card-body">
                                <p>{{ user.email }}<br/>
                                    {{ user.address_1 }}<br/>
                                    {{ user.city }}, {{ user.region }}</p>
                                <p v-if="typeof user.professional_certificate_bc !== 'undefined' && user.professional_certificate_bc.length > 1">
                                    <strong>BC Professional Certificate:</strong> {{ user.professional_certificate_bc }}
                                </p>
                                <p v-if="typeof user.professional_certificate_yk !== 'undefined' && user.professional_certificate_yk.length > 1">
                                    <strong>Yukon Professional Certificate:</strong> {{ user.professional_certificate_yk
                                    }}</p>
                                <p v-if="typeof user.professional_certificate_other !== 'undefined' && user.professional_certificate_other.length > 1">
                                    <strong>Other Certificate:</strong> {{ user.professional_certificate_other }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card">
                            <div class="card-header">
                                <h2>Credentials</h2>
                            </div>
                            <div class="card-body">
                                <div class="row" v-for="credential in credentials_local">
                                    <div class="col-1"><i class="fas fa-igloo"></i></div>
                                    <div class="col">{{ credential.name }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-1">
                                        <button class="btn btn-primary btn-sm" @click="addCredential">+</button>
                                    </div>
                                    <div class="col">
                                        <select v-model="new_credential">
                                            <option value="0">Select New Credential</option>
                                            <option v-for="credential in credentials" :value="credential.id">{{
                                                credential.name }}
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
                                        <td>{{ session.dates }}</td>
                                        <td>{{ session.location }}</td>
                                        <td>
                                            <template v-if="isStatus(session, 'Invited')">Accept Invitation!</template>
                                            <template v-else-if="isStatus(session, 'Scheduled')">You're Going!</template>
                                            <template v-else-if="isStatus(session, 'Applied')">You've Applied</template>
                                            <template v-else-if="isStatus(session, 'Declined')">Declined</template>
                                            <template v-else-if="isStatus(session, 'Open')">Open</template>
                                        </td>
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
    </div>
</template>

<script>
    // import { mapMutations } from 'vuex'
    // import { mapGetters } from 'vuex'

    export default {
        name: "Dashboard",
        props: {
            user: {},
            credentials: {},
            sessions: {},
            subjects: {},
            schools: {
                type: Object
            }
        },
        data() {
            return {
                sessions_local: [],
                credentials_local: [],
                new_credential: 0,
                filter: '',
                current_session: {}
            }
        },
        mounted() {
            console.log('Dashboard Mounted')
            this.sessions_local = this.sessions
            Event.listen('credential-added', this.pushCredential)
        },
        computed: {
        },
        methods: {
            addCredential: function () {
                console.log('adding credential')

                var form = this

                axios.post('/Dashboard/credential', {
                    credential_id: form.new_credential
                })
                    .then(function (response) {
                        Event.fire('credential-added', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                    });
            },
            countStatus: function (status) {
                // var status
                return Object.values(this.sessions_local).filter(function (assignment) {
                    return assignment.status == status
                }).length
            },
            pushCredential(credential) {
                console.log('pushing credential')
                this.credentials_local.unshift(credential)
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

            viewSession(session) {
                console.log('View Session')
                this.current_session = session
                this.$modal.show('session_form');
            },
            closeModal() {
                this.$modal.hide('session_form');
            }

        }

    }
</script>

<style>
</style>
