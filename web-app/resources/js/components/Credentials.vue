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
                        <font-awesome-icon v-else icon="trash" @click="removeCredential(credential)"
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
import {mapGetters} from 'vuex'
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
        this.credentials_applied = this.getCredentials;
    },
    computed: {
         ...mapGetters([
            'getCredentials'
        ]),

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
    },
    methods: {
        closeModal() {
            this.$modal.hide('credentials_form');
        },
        cancelCredential() {
            this.closeModal()
        },
        updateUserCredentialsInStore() {
            console.log('updateUserCredentials in store', this.credentials_applied);
            this.$store.commit('SET_CREDENTIALS', this.credentials_applied);
        },
        filterCredential(credential_id) {
            console.log('removeCredential', credential_id);

            // Get the credential
            this.credentials_applied = this.credentials_applied.filter(item => item.credential.id !== credential_id);
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
        async addCredential() {
            console.log('adding credential' + JSON.stringify(axios.defaults));

            this.working = true;
            const profile_credential = await this.postCredential();
            this.working = false;
            if (!!profile_credential) {
                this.pushCredential(profile_credential);
                this.updateUserCredentialsInStore();
                console.log('Refresh credentials added!', profile_credential);
            }
        },
        postCredential() {
            return axios.post('/api/'+this.user.id+'/profile-credentials', {
              credential_id: this.new_credential.credential.id,
              year: this.new_credential.year
            })
            .then(response => {
                console.log('Create Success!', response.data);
                return response.data;
            })
            .catch(error => {
                console.log('Failure!', error);
                return {};
            });
        },
        async removeCredential(profile_credential) {
            console.log('removing credential' + JSON.stringify(axios.defaults));

            this.working = true;
            const response_id = await this.deleteCredential(profile_credential);
            this.working = false;
            if (!!response_id) {
                this.filterCredential(profile_credential.credential.id);
                this.updateUserCredentialsInStore();
                console.log('Refresh credentials removed!', profile_credential);
            }
           
        },
        deleteCredential(profile_credential) {
            return axios.delete('/api/'+this.user.id+'/profile-credentials/'+profile_credential.id)
            .then(response => {
                console.log('Delete Success!', profile_credential.credential.id);
                return profile_credential.id;
            })
            .catch(error => {
                console.log('Failure!', error);
                return '';
            });
        }

    }
}
</script>