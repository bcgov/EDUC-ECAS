<template>

    <div class="col">
        <div class="card">
            <div class="card-header">
                <h2>Credentials</h2>
            </div>
            <div class="card-body">
                <div class="row" v-for="credential in credentials_applied">
                    <div class="col-1 text-center">
                        <font-awesome-icon v-if="credential.verified" icon="check" alt="verified"/>
                        <font-awesome-icon v-else icon="trash" @click="deleteCredential(credential)"
                                           alt="delete" style="color: red;"/>
                    </div>
                    <div class="col">{{ credential.credential.name }}</div>
                </div>
                <div class="row pt-3">
                    <div class="col-1">
                        <button class="btn btn-primary btn-sm" @click="addCredential">
                            <span>
                                <div class="loader text-center" v-show="working"></div>
                            </span>
                            <div v-show="!working">+</div>
                        </button>
                    </div>
                    <div class="col">
                        <select v-model="new_credential">
                            <option value="0">Select New Credential</option>
                            <option v-for="credential in credentials_available" :value="credential.id">
                                {{ credential.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>

    export default {
        name: "ProfileCredentials",
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
            console.log('Profile Credentials Mounted')


            Event.listen('credential-added', this.pushCredential)
            Event.listen('credential-deleted', this.removeCredential)

        },

        methods: {
            addCredential() {
                console.log('adding credential')

                this.working = true

                var form = this

                axios.post('/api/profile-credentials', {
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
                        console.log('Failure!', error)
                    });
            },
            deleteCredential(profile_credential) {
                console.log('removing credential')

                this.working = true

                var form = this

                axios.post('/api/profile-credentials/delete', {
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


        }

    }
</script>
