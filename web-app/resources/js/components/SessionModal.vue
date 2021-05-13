<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>{{ session.type.name }} - {{ session.activity.name }}</h2>
            <h3>{{ session.date }}, {{ session.location }}</h3>
        </div>
        <div class="card-body">
            <template v-if="isStatus(session, 'Open')">
                <p>This Session is open.</p>
                <p>Would you to like to apply for this Session?</p>
            </template>
            <template v-else-if="isStatus(session, 'Applied')">
                <p>
                    Thank you for your application for this session. You will be contacted by email if you are
		    selected to participate. If you need to un-apply please click the box below.
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Invited')">
                <p>You are invited to participate in this session.</p>
                <p v-if="hasSIN">Please accept or decline as soon as possible.</p>
                <p v-else>
                    <a @click.prevent="editProfile" href="">
                        You must include a Social Insurance Number in your
                        profile to attend a Session!
                    </a>
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Accepted')">
                <p>
                    Thank you for accepting your invitation to participate in this session. A contract is being created
                    and you will be notified when it is ready for signing. If you need to cancel your participation
                    before receiving the contract, please check the box below.
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Declined')">
                <p>You have declined your invitation for this session.</p>
            </template>
            <template v-else-if="isStatus(session, 'Contract')">
                <p>
                    Your contract is ready for signature. Please download, sign, date, and scan back to exams@gov.bc.ca
                    as soon as possible. If you are no longer able to participate in this session, please Decline
                    below.
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Confirmed')">
                <p>
                    Thank you for returning your signed contract. If you need to withdraw, please contact exams@gov.bc.ca
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Completed')">
                <p>
                    This session is now complete. If there are fees or expenses attached to the session please submit
                    receipts if required. Payments can be expected within 4-6 weeks. If you have any questions, please
                    contact exams@gov.bc.ca
                </p>
            </template>
            <template v-else-if="isStatus(session, 'Withdrew')">
                <p>You have voluntarily withdrawn from this session.</p>
            </template>
            <template v-else-if="isStatus(session, 'Attendance Recorded')">
                <p>Your attendance at this session has been recorded.</p>
            </template>
            <template v-else-if="isStatus(session, 'Attended')">
                <p>Your attendance at this session has been recorded.</p>
            </template>
                <template v-else>
                    <p>Unknown Session Status</p>
                </template>
        </div>
        <div class="card-footer">
            <div class="row">
                <template v-if="isStatus(session, 'Open')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="closeModal()">
                           No thank you</button>
                    </div>
                    <div class="col">
                        <button v-show=" ! working" class="btn btn-primary btn-block" v-on:click="applyToSession(session, true)">
                            Yes please</button>
                        <span>
                                <div class="loader text-center" v-show="working"></div>
                            </span>
                    </div>
                </template>
                <template v-if="isStatus(session, 'Applied')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="applyToSession(session, false)">
                            Un-apply</button>
                    </div>
                </template>
                <template v-else-if="isStatus(session, 'Invited')">
					<div class="col">
                        <button v-if="hasSIN" class="btn btn-primary btn-block" v-on:click="acceptInvitation(session, true )">
                            Accept</button>
                        <button v-else class="btn btn-primary btn-block" v-on:click="editProfile">
                            Add SIN</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(session, false)">
                            Decline</button>
                    </div>
                </template>
                <template v-else-if="isStatus(session, 'Accepted') || isStatus(session, 'Contract')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(session, false)">
                            Decline</button>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex'

    export default {
        name: "SessionModal",
        props: {
            session: {}
        },
        data() {
            return {
                session_local: [],
                working: false,
            }
        },
        mounted() {
            console.log('Session Mounted')
        },
        computed: {
            ...mapGetters([
                'getUser'
            ]),
            hasSIN() {
                return this.getUser.is_SIN_on_file;
            }
        },
        methods: {
            closeModal() {
                this.$modal.hide('session_form');
            },

            editProfile() {
                Event.fire('launch-profile-modal');
            },

            isStatus: function (session, status) {
                return session.assignment.status.name === status
            },

            acceptInvitation(session, accept) {

                let action = accept ? 'Accepted' : 'Declined';

                this.updateAssignment(session, action);
				},

            applyToSession (session, attend) {

                if(attend) {
                    this.createAssignment(session);
                }

                if(! attend) {
                    this.updateAssignment(session, 'Withdrew')
                }
            },

          createAssignment(session) {

                var form = this;

                if (this.working) {
                    return
                }

                form.working = true;

                axios.post('/api/' + form.getUser.id + '/assignments', {
                    session_id:     session.id
                })
                    .then(function (response) {
                        console.log('assignment created: ', response.data.data);
                        Event.fire('session_status_updated', response.data.data );
                        session.status = response.data.data.status;
                        form.closeModal();
                        form.working = false;
                        console.log('Event: refresh-sessions-data is fired!!!');
						Event.fire('refresh-sessions-data');
                    })
                    .catch(function (error) {
                        console.log('Failure!', error);
                        form.working = false;
                    });
           },
            updateAssignment(session, action) {

                var form = this;

                if (this.working) {
                    return
                }

                console.log('updateAssignment', action, session.status);

                // Only post if something needs to be done
                if (action !== session.assignment.status.name ) {

                    form.working = true;

                    axios.patch('/api/' + form.getUser.id + '/assignments/' + session.assignment.id, {
                        session_id:     session.id,
                        action:         action
                    })
                        .then(function (response) {
                            console.log('assignment updated: ', response.data.data);
                            Event.fire('session_status_updated', response.data.data );
                            session.status = response.data.data.status;
                            form.closeModal();
                            form.working = false;
							console.log('assignment update success')
                        })
                        .catch(function (error) {
                            console.log('Update Session Failure!', error);
                            form.working = false;
                        });
                }
            },

        }
    }
</script>
