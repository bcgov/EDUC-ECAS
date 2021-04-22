<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Credentials</h2>
        </div>
        <div class="card-body">
            <form v-on:keydown.enter.prevent>
                <div class="row" v-for="credential in credentials_applied">
                    <div class="col-1 text-center">
                        <font-awesome-icon v-if="credential.verified" icon="check" alt="verified"/>
                        <font-awesome-icon v-else icon="trash" @click="deleteCredential(credential)"
                                            alt="delete" style="color: red;"/>
                    </div>
                    <div class="col-11">{{ credential.credential.name }}
                        <span v-if="credential.year" class="col">Year: {{ credential.year }}</span>
                    </div>
                </div>
                <div class="row pt-3 pl-4">
                    <div class="form-row">
                        <div class="form-group col-sm-5">
                            <label for="new_credential_year">Credential</label>
                            <select v-model="new_credential.credential" class="form-control form-control-sm">
                                <option value="0">Select New Credential</option>
                                <option v-for="credential in credentialsAvailable" :value="credential">
                                    {{ credential.name }}
                                </option>
                            </select>
                        </div>
                        <div class="form-group col-sm-4">
                            <label for="new_credential_year">Year certified</label>
                            <input v-model="new_credential.year" type="text" class="form-control form-control-sm" name="new_credential_year"
                                    id="new_credential_year" placeholder="YYYY">

                        </div>
                        <button class="btn btn-primary btn-sm mt-4 mb-3" @click="addCredential()">
                            <span>
                                <div class="loader text-center" v-show="working"></div>
                            </span>
                            <div v-show="!working">Add</div>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
 import axios from 'axios';

export default {
    name: "Credentials",

    props: {
        user: {
            type: Object,
            required: true
        },
        credentials: {
            type: Array,
            required: true
        },
        user_credentials: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            credentials_applied: [],
            blank_credential: {
                credential: 0,
                year: null
            },
            new_credential: {
                credential: 0
            },
            working: false,
        }
    },
    mounted() {
        this.credentials_applied = this.user_credentials;
    },
    watch: {
        user_credentials(newValue) {
            this.credentials_applied = newValue;
        },
    },
    computed: {
        
    },
    methods: {
        closeModal() {
            this.$modal.hide('credentials_form');
        },
        cancelCredential() {
            this.closeModal()
        },
        credentialsIdsInUse() {
            var arrayOfCredentialIds = [];

            this.credentials_applied.forEach( function (applied) {
                arrayOfCredentialIds.push(applied.credential.id);
            });

            return arrayOfCredentialIds;
        },

        credentialsAvailable() {
            // subtract applied_credentials from credentials
            return this.credentials.filter(x => ! this.credentialsIdsInUse.includes(x.id));

        },
        addCredential() {
            console.log('adding credential' + JSON.stringify(axios.defaults));

            this.working = true;

            var form = this;

            console.log('form', form );

            axios.post('/api/' + this.user.id + '/profile-credentials', {
                credential_id: this.new_credential.credential.id,
                year: this.new_credential.year
            })
                .then(function (response) {
                    form.working = false;
                    this.pushCredential(response.data);
                    Event.fire('user-credentials-updated', this.credentials_applied);
                    console.log('Create Success!', response.data);
                })
                .catch(function (error) {
                    form.working = false;
                    console.log('Failure!', error)
                });
        },
        deleteCredential(profile_credential) {
            console.log('removing credential' + JSON.stringify(axios.defaults));

            this.working = true;

            var form = this;

            axios.delete('/api/' + this.user.id + '/profile-credentials/' + profile_credential.id )
                .then(function (response) {
                    form.working = false;
                    this.removeCredential(profile_credential.credential.id);
                    Event.fire('user-credentials-updated', this.credentials_applied);
                    console.log('Delete Success!', profile_credential.credential.id )
                })
                .catch(function (error) {
                    form.working = false;
                    console.log('Failure!')
                });
        },
        pushCredential(profile_credential) {
            console.log('pushing credential', profile_credential.data.credential.id );

            // Add to the applied list
            this.credentials_applied.push(profile_credential.data);

            this.new_credential = {
                credential: 0,
                year: null
            }
        },
        removeCredential(profile_credential) {
            console.log('removeCredential', profile_credential);

            // Get the credential
            let index = this.credentials_applied.findIndex(credential => credential.credential.id === profile_credential);

            console.log('get the credential', index);

            let credential = this.credentials_applied[index].credential;

            // Remove the credential from the applied list
            this.credentials_applied.splice(index, 1);


            //this.new_credential = this.blank_credential;
        },
    }
}
</script>