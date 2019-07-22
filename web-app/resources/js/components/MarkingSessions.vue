<template>


        <div class="card">
            <div class="card-header pb-0">
                <h2 class="float-left">Marking Sessions</h2>
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
                           @click="filter = 'Confirmed'"
                           class="nav-link"
                           :class="{ 'active': filter === 'Confirmed' }">Going
                            <span class="badge badge-pill badge-primary">{{ countStatus('Confirmed') }}</span></a>
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
                        <td>{{ session.type.name }}</td>
                        <td>{{ session.activity.name }}</td>
                        <td nowrap>{{ session.date }}</td>
                        <td>{{ session.location }}</td>
                        <td>{{ sessionStatus(session) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <modal name="session_form" height="auto">
                <session-modal :session="current_session"></session-modal>
            </modal>
        </div>


</template>

<script>
    import {mapGetters} from 'vuex'
    import SessionModal from "./SessionModal";

    export default {
        name: "MarkingSessions",
        components: {SessionModal},
        props: {
            data: {},
        },
        data() {
            return {
                credentials_applied: [...this.data.user_credentials],
                credentials_available: [...this.data.credentials],
                new_credential: 0,
                filter: '',
                current_session: {},
                new_user: false,
                working: false
            }
        },
        mounted() {
            console.log('Dashboard Mounted')

            this.$store.commit('SET_USER', this.data.user)
            this.$store.commit('SET_SESSIONS', this.data.sessions)

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
                    if (dashboard.filter.length === 0) {
                        return true
                    }
                    return session.status === dashboard.filter
                })
            }
        },
        methods: {

            countStatus(status) {
                // var status
                return Object.values(this.getSessions).filter(function (assignment) {
                    return assignment.status === status
                }).length
            },

            isStatus: function (session, status) {
                return session.status === status
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

            updateSessionStatus(response) {
                this.$store.commit('UPDATE_SESSION_STATUS', response)
            }
        }

    }
</script>

