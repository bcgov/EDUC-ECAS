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
                <p>Thank you for your application for this session. The Ministry is reviewing all applications for eligibility and will notify you one way or the other. If you need to un-apply please check the box below.</p>
            </template>
            <template v-else-if="isStatus(session, 'Invited')">
                <p>You are invited to participate in this session.</p>
                <p v-if="hasSIN">Please accept or decline as soon as possible.</p>
                <p v-else><a @click.prevent="editProfile" href="">You must include a Social Insurance Number in your profile to attend a Session!</a></p>
            </template>
            <template v-else-if="isStatus(session, 'Accepted')">
                <p>Thank you for accepting your invitation to participate in this session. A contract is being created and you will be notified when it is ready for signing. If you need to cancel your participation before receiving the contract, please check the box below.</p>
            </template>
            <template v-else-if="isStatus(session, 'Declined')">
                <p>You have declined your invitation for this session.</p>
            </template>
            <template v-else-if="isStatus(session, 'Contract')">
                <p>Your contract is ready for signature. Please download, sign, dated, and scan back to exams@gov.bc.ca as soon as possible. If you are no longer able to participate in this session, please Decline below.</p>
            </template>
            <template v-else-if="isStatus(session, 'Confirmed')">
                <p>Thank you for returning your signed contract. At this point the Ministry is relying on you to participate in the session. In an unexpected situation comes up and you need to withdraw, please contact exams@gov.bc.ca</p>
            </template>
            <template v-else-if="isStatus(session, 'Completed')">
                <p>This session is now complete. If there are fees or expenses attached to the session please submit receipts if required. Payments can be expected within 4-6 weeks. If you have any questions, please contact exams@gov.bc.ca</p>
            </template>
            <template v-else>
                <p>Unknown Session Status</p>
            </template>
        </div>
        <div class="card-footer">
            <div class="row">
                <template v-if="isStatus(session, 'Open')">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click="applyToSession(session, false)">
                            No thank you</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block" v-on:click="applyToSession(session)">
                            Yes please</button>
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
                        <button class="btn btn-danger btn-block" v-on:click="acceptInvitation(session, false)">
                            Decline</button>
                    </div>
                    <div class="col">
                        <button v-if="hasSIN" class="btn btn-primary btn-block" v-on:click="acceptInvitation(session)">
                            Accept</button>
                        <button v-else class="btn btn-primary btn-block" v-on:click="editProfile">
                            Add SIN</button>
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
            ]),
            hasSIN() {
                return this.getUser.social_insurance_number ? true : false
            }
        },
        methods: {
            closeModal() {
                this.$modal.hide('session_form');
            },
            editProfile() {
                this.closeModal()
                this.$modal.show('profile_form');
            },

            isStatus: function (session, status) {
                return session.status == status
            },
            acceptInvitation(session, accept) {

                // If nothing passed in assume user wants to accept
                if (accept === undefined) accept = true;

                let action = accept ? 'Accepted' : 'Declined'

                this.postSession(session, action)
            },
            applyToSession (session, attend) {

                // If nothing passed in assume user wants to attend
                if (attend === undefined) attend = true;

                let action = attend ? 'Applied' : 'Open'

                this.postSession(session, action)
            },
            postSession(session, action) {

                this.closeModal()

                var form = this

                // Only post if something needs to be done
                if (action !== session.status) {

                    // assume success! and change the status before we post
                    // TODO: should handle failure gracefully!
                    session.status = action

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
                }
            }
        }
    }
</script>