<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>{{ session.type }} - {{ session.activity }}</h2>
            <h3>{{ session.dates }}, {{ session.location }}</h3>
        </div>
        <div class="card-body">
            <template v-if="isStatus(session, 'Open')">
                <p>This Session is open.</p>
                <p>Would you to like to apply for this Session?</p>
            </template>
            <template v-else-if="isStatus(session, 'Applied')">
                <p>You have applied to this Session</p>
                <p>Do you still want to go?</p>
            </template>
            <template v-else-if="isStatus(session, 'Invited')">
                <p>You are invited to participate in this {{ session.activity }} Session!</p>
                <p>Please accept the invitation to confirm your attendance.</p>
            </template>
            <template v-else-if="isStatus(session, 'Scheduled')">
                <p>You have accepted the invitation to this session and are scheduled to attend.</p>
                <p>Your signed contract has not been received. You may download a copy below.</p>
                <p>If you can no longer attend please cancel.</p>
            </template>
            <template v-else-if="isStatus(session, 'Contracted')">
                You have have a contract and are scheduled to attend this session.
            </template>
        </div>
        <div class="card-footer">
            <div class="row">
                <template v-if="isStatus(session, 'Open') || isStatus(session, 'Applied')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="applyToSession(session, false)">
                            No, Thanks</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block"
                                v-on:click="acceptInvitation(session)">Yes, Please</button>
                    </div>
                </template>
                <template v-else-if="isStatus(session, 'Invited')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(session, false)">No, Thanks</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block"
                                v-on:click="acceptInvitation(session)">Accept Invitation!</button>
                    </div>
                </template>
                <template v-else-if="isStatus(session, 'Scheduled')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(session, false)">Cancel Attendance!</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block" v-on:click="getExpenses(session)">Expenses</button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: "Session",
        props: {
            session: {}
        },
        data() {
            return {
                session_local: []
            }
        },
        mounted() {
            console.log('Session Mounted')
        },
        computed: {
            ...mapGetters([
                'getUser'
            ])
        },
        methods: {
            closeModal() {
                this.$modal.hide('session_form');
            },
            isStatus: function (session, status) {
                return session.status == status
            },
            acceptInvitation(session) {
                session.status = 'Accepted'
                this.postSession(session, 'Accepted')
            },
            applyToSession (session, attend) {

                if (attend === undefined) attend = true;

                if (attend) {
                    session.status = 'Applied'
                    this.postSession(session, 'Applied')
                }
                else {
                    session.status = 'Open'
                    this.postSession(session, 'Open')
                }
            },
            postSession(session, action) {

                this.closeModal()

                var form = this

                axios.post('/Dashboard/session', {
                    assignment_id: session.assignment_id,
                    session_id: session.id,
                    user_id: form.getUser.id,
                    action: action
                })
                    .then(function (response) {
                        Event.fire('session_status_updated', response.data)
                        console.log(response.data)
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                    });
            },
            getContract() {
                console.log('Download Contract')
                this.closeModal();
            },
            getExpenses(session) {
                window.location = "/Expenses/" + session.id;
            }
        }
    }
</script>

<style scoped>

</style>