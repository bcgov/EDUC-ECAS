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
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
                    <h2>{{ current_session.type }} - {{ current_session.activity }}</h2>
                    <h3>{{ current_session.dates }}, {{ current_session.location }}</h3>
                </div>
                <div class="card-body">
                    <template v-if="isStatus(current_session, 'Open')">
                        <p>This Session is open.</p>
                        <p>Would you to like to apply for this Session?</p>
                    </template>
                    <template v-else-if="isStatus(current_session, 'Applied')">
                        <p>You have applied to this Session</p>
                        <p>Do you still want to go?</p>
                    </template>
                    <template v-else-if="isStatus(current_session, 'Invited')">
                        <p>You are invited to participate in this {{ current_session.activity }} Session!</p>
                        <p>Please accept the invitation to confirm your attendance.</p>
                    </template>
                    <template v-else-if="isStatus(current_session, 'Scheduled')">
                        <p>You have accepted the invitation to this session and are scheduled to attend.</p>
                        <p>Your signed contract has not been received. You may download a copy below.</p>
                        <p>If you can no longer attend please cancel.</p>
                    </template>
                    <template v-else-if="isStatus(current_session, 'Contracted')">
                        You have have a contract and are scheduled to attend this session.
                    </template>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <template v-if="isStatus(current_session, 'Open') || isStatus(current_session, 'Applied')">
                            <div class="col">
                                <button class="btn btn-danger btn-block" v-on:click="applyToSession(current_session, false)">
                                    No, Thanks</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-block"
                                        v-on:click="applyToSession(current_session)">Yes, Please</button>
                            </div>
                        </template>
                        <template v-else-if="isStatus(current_session, 'Invited')">
                            <div class="col">
                                <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(current_session, false)">No, Thanks</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-block"
                                        v-on:click="acceptInvitation(current_session)">Accept Invitation!</button>
                            </div>
                        </template>
                        <template v-else-if="isStatus(current_session, 'Scheduled')">
                            <div class="col">
                                <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(current_session, false)">Cancel Attendance!</button>
                            </div>
                            <div class="col">
                                <button class="btn btn-primary btn-block" v-on:click="getContract">Download Contract</button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

        </modal>
    </div>
</template>

<script>
    export default {
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
            acceptInvitation: function (session, accept) {

                if (accept === undefined) accept = true;

                this.closeModal()

                if (accept) {
                    console.log('accepting assignment ' + session.id)
                    session.status = 'Scheduled'
                }
                else {
                    console.log('declining invitation ' + session.id)
                    session.status = 'Declined'
                }
            },
            applyToSession: function (session, attend) {

                if (attend === undefined) attend = true;

                this.closeModal()

                if (attend) {
                    console.log('applying to session ' + session.id)
                    session.status = 'Applied'
                }
                else {
                    console.log('cancelling application to session ' + session.id)
                    session.status = 'Open'
                }
            },
            viewSession(session) {
                console.log('View Session')
                this.current_session = session
                this.$modal.show('session_form');
            },
            closeModal() {
                this.$modal.hide('session_form');
            },
            getContract() {
                console.log('Download Contract')
                this.closeModal();
            }
        }

    }
</script>

<style>
</style>
