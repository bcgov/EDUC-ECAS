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
                                <div class="row">
                                    <div class="col"><h2>Marking Sessions</h2></div>
                                    <div class="col text-right">
                                        <button @click="filter = 'Scheduled'" class="btn btn-success">
                                            You're Going! <span class="badge badge-light">{{ goingCount }}</span>
                                        </button>
                                        <button class="btn btn-primary">
                                            You're Invited! <span class="badge badge-light">2</span>
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th>Activity</th>
                                        <th>Type</th>
                                        <th>Dates</th>
                                        <th>Location</th>
                                        <th>Status</th>
                                    </tr>
                                    <tr v-for="(session, id) in filteredSessions(sessions)">
                                        <td>{{ session.activity }}</td>
                                        <td>{{ session.type }}</td>
                                        <td>{{ session.dates }}</td>
                                        <td>{{ session.location }}</td>
                                        <td>
                                            <template v-if="isAssigned(id)">
                                                <button class="btn btn-primary btn-sm"
                                                        v-on:click="acceptInvitation(id)">Accept Invitation!
                                                </button>
                                            </template>
                                            <template v-else-if="isScheduled(id)">You're Going!</template>
                                            <template v-else>Open</template>
                                            <!-- <button class="btn btn-success btn-sm">Contract Pending</button> -->
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <modal name="credentials_form">
            Modal Form
        </modal>
    </div>
</template>

<script>
    export default {
        props: {
            user: {},
            credentials: {},
            sessions: {},
            assignments: {},
            subjects: {},
            schools: {
                type: Object
            }
        },
        data() {
            return {
                assignments_local: [],
                credentials_local: [],
                new_credential: 0,
                filter: ''
            }
        },
        mounted() {
            console.log('Dashboard Mounted')
            this.assignments_local = this.assignments
            // this.credentials_local = this.credentials
            Event.listen('credential-added', this.pushCredential)
        },
        computed: {
            goingCount: function () {
                return Object.values(this.assignments_local).filter(function (assignment) {
                    return assignment.status == 'Scheduled'
                }).length
            }
        },

        methods: {
            addCredential: function () {
                console.log('adding credential')
                // this.$modal.hide('credentials_form');

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
            isAssigned: function (id) {
                if (typeof(this.assignments_local[id]) !== 'undefined') {
                    return this.assignments_local[id].status == 'Assigned'
                }

                return false
            },
            isScheduled: function (id) {
                if (typeof(this.assignments_local[id]) !== 'undefined') {
                    return this.assignments_local[id].status == 'Scheduled'
                }

                return false
            },
            acceptInvitation: function (id) {
                console.log('accepting assignment ' + id)
                this.assignments_local[id].status = 'Scheduled'
            },
            editCredentials() {
                console.log('Edit Profile')
                this.$modal.show('credentials_form');
            }
        }

    }
</script>

<style>
</style>
