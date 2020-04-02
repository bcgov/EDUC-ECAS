<template>
    <div>
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-6">
                                <h2>Marking Sessions</h2>
                            </div>
                            <div class="col-6 text-right">
                                <button class="btn btn-link btn-sm " v-text="showPastSessionText" @click="showPastSessions = ! showPastSessions"></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                Click on a session below to apply, accept or decline
                            </div>
                            <div class="col-6">

                                <ul class="nav nav-tabs justify-content-end pt-2">
                                    <li class="nav-item mb-0">
                                        <a @click="filter = ''"
                                           class="nav-link"
                                           :class="{ 'active': filter === '' }">All
                                            <span class="badge badge-pill badge-primary">{{ dateFilteredSessions.length }}</span></a>
                                    </li>
                                    <li v-for="status in uniqueSessionStatus" class="nav-item mb-0">
                                        <a @click="filter = status"
                                           class="nav-link"
                                           :class="{ 'active': filter === status }">{{ status }}
                                            <span class="badge badge-pill badge-primary">{{ countStatus(status) }}</span></a>
                                    </li>
                                </ul>
                            </div>
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
                                <td>{{ sessionStatus(session.assignment) }}</td>
                            </tr>
                            </tbody>
                        </table>
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


    export default {
        name: "MarkingSessions",
        props: {
            sessions: null,
        },
        data() {
            return {

                showPastSessions: false,

                filter: '',
                current_session: {},

                working: false,

            }
        },
        mounted() {
            console.log('MarkingSessions Mounted');

            Event.listen('session_status_updated', this.updateSessionStatus);

        },

        computed: {

            publicSessions() {

                // The Ministry business rules require that Sessions on the portal be shown only if the
                // session is published and "Open" OR the user has been assignment related to the session.

                return this.sessions.filter( function (session) {
                    return (session.is_published && session.session_status === "Open") || session.assignment.id !== 0;

                })
            },

            replaceSelected() {

                // The Ministry business rules require that 'Selected" assignments are
                // displayed as 'Applied'

                let sanitized = [];

                this.publicSessions.forEach( function (session) {
                     if(session.assignment.status.name === 'Selected') {
                        session.assignment.status.name = "Applied"
                     }

                     sanitized.push(session);
                });

                return sanitized;
            },


            dateFilteredSessions() {

                if(this.showPastSessions) {
                    // display all sessions
                    return this.replaceSelected;
                }

                return this.replaceSelected.filter( function (session) {

                    return ! session.isPast;

                });
            },

            sortSessionByEndDate() {
                return this.dateFilteredSessions.sort(function (a, b) {
                    return parseInt(a.diff_in_days) - parseInt(b.diff_in_days);
                })
            },

            filteredSessions() {
                var dashboard = this;
                return this.sortSessionByEndDate.filter(function (session) {
                    if (dashboard.filter.length === 0) {
                        return true
                    }
                    return session.assignment.status.name === dashboard.filter
                })
            },

            uniqueSessionStatus() {
                let array =  [...new Set(this.dateFilteredSessions.map(session => session.assignment.status.name))];

                return array.sort();

            },

            showPastSessionText() {
                if(this.showPastSessions) {
                    return 'Show current sessions';
                }

                return 'Show current and past sessions';
            }


        },
        methods: {

            countStatus(status) {
                // var status
                return Object.values(this.dateFilteredSessions).filter(function (session) {
                    return session.assignment.status.name === status
                }).length
            },


            sessionStatus(assignment) {
                switch (assignment.status.name) {
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
                console.log('View Session');
                this.current_session = session;
                this.$modal.show('session_form');
            },
            closeModal() {
                this.$modal.hide('session_form');
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
