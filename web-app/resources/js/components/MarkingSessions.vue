<template>

<div class="card">
    <div class="card-header pb-0">
        <h2 class="float-left">Marking Sessions</h2>
        <ul class="nav nav-tabs justify-content-end pt-2">
            <li class="nav-item mb-0">
                <a href="#"
                   @click="filter = ''"
                   class="nav-link"
                   :class="{ 'active': filter == '' }">All
                    <span class="badge badge-pill badge-primary">{{ getSessions.length }}</span></a>
            </li>
            <li class="nav-item mb-0">
                <a href="#"
                   @click="filter = 'Applied'"
                   class="nav-link"
                   :class="{ 'active': filter == 'Applied' }">Applied
                    <span class="badge badge-pill badge-primary">{{ countStatus('Applied') }}</span></a>
            </li>
            <li class="nav-item mb-0">
                <a href="#"
                   @click="filter = 'Invited'"
                   class="nav-link"
                   :class="{ 'active': filter == 'Invited' }">Invited
                    <span class="badge badge-pill badge-primary">{{ countStatus('Invited') }}</span></a>
            </li>
            <li class="nav-item mb-0">
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
                v-for="session in filteredSessions">
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
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "MarkingSession",
        props: {
            aggregated: {},

        },
        data() {
            return {
                credentials_applied: [...this.user_credentials],
                credentials_available: [...this.credentials],
                new_credential: 0,
                filter: '',
                current_session: {},
                new_user: false,
                working: false
            }
        },
        mounted() {
            console.log('Dashboard Mounted');

            this.$store.commit('SET_USER', this.user)
            this.$store.commit('SET_SESSIONS', this.sessions)

            Event.listen('credential-added', this.pushCredential)
            Event.listen('credential-deleted', this.removeCredential)
            Event.listen('profile-updated', this.updateProfile)
            Event.listen('session_status_updated', this.updateSessionStatus)

            if (this.getUser.id === undefined) {
                this.new_user = true
                this.showProfile()
            }
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
                    if (dashboard.filter.length == 0) {
                        return true
                    }
                    return session.status == dashboard.filter
                })
            }
        },
        methods: {
            addCredential() {
                console.log('adding credential')

                this.working = true

                var form = this

                axios.post('/Dashboard/credential', {
                    credential_id: form.new_credential,
                    user_id: form.getUser.id
                })
                    .then(function (response) {
                        form.working = false
                        Event.fire('credential-added', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        form.working = false
                        console.log('Failure!')
                    });
            },
            deleteCredential(profile_credential) {
                console.log('removing credential')

                this.working = true

                var form = this

                axios.post('/Dashboard/credential/delete', {
                    profile_credential_id: profile_credential.id
                })
                    .then(function (response) {
                        form.working = false
                        Event.fire('credential-deleted', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        form.working = false
                        console.log('Failure!')
                    });
            },
            countStatus(status) {
                // var status
                return Object.values(this.getSessions).filter(function (assignment) {
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
            },
            updateSessionStatus(response) {
                this.$store.commit('UPDATE_SESSION_STATUS', response)
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
