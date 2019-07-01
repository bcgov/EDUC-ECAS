<template>
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
                {{ getUser.city }}, {{ getUser.region }} {{ getUser.postal_code }}
            </p>
            <p v-if="getUser.professional_certificate_bc">
                <strong>BC Professional Certificate:</strong> {{ getUser.professional_certificate_bc
                }}
            </p>
            <p v-if="getUser.professional_certificate_yk">
                <strong>Yukon Professional Certificate:</strong> {{
                getUser.professional_certificate_yk }}</p>
            <p v-if="getUser.professional_certificate_other">
                <strong>Other Certificate:</strong> {{ getUser.professional_certificate_other }}</p>
            <p v-if="getUser.district">
                <strong>District:</strong> {{ getUser.district }}</p>
            <p v-if="getUser.school">
                <strong>School:</strong> {{ getUser.school }}</p>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        name: "profile-component",
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
                credentials_available: [...this.credentials],
                new_credential: 0,
                filter: '',
                current_session: {},
                new_user: false,
                working: false
            }
        },
        mounted() {
            console.log('Dashboard Mounted')

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
