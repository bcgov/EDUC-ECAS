<template>
    <div class="card">
        <div class="card-header"><h1>Edit Profile</h1></div>
        <div class="card-body">
            <form>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="preferred_first_name">Preferred First</label>
                        <input v-model="user_local.preferred_first_name"
                               type="text" class="form-control"
                               name="preferred_first_name" id="preferred_first_name">
                    </div>
                    <div class="form-group col">
                        <label for="first_name">First</label>
                        <input v-model="user_local.first_name" type="text" class="form-control" name="first_name"
                               id="first_name">
                    </div>
                    <div class="form-group col">
                        <label for="last_name">Last</label>
                        <input v-model="user_local.last_name" type="text" class="form-control" name="last_name"
                               id="last_name">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="email">email</label>
                        <input v-model="user_local.email" type="text" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group col">
                        <label for="social_insurance_no">S.I.N.</label>
                        <input v-model="user_local.social_insurance_no" type="text" class="form-control"
                               name="social_insurance_no" id="social_insurance_no">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="address_1">Address</label>
                            <input v-model="user_local.address_1" type="text" class="form-control" name="address_1"
                                   id="address_1">
                        </div>
                        <div class="form-group">
                            <label for="address_2">Line 2</label>
                            <input v-model="user_local.address_2" type="text" class="form-control" name="address_2"
                                   id="address_2">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="city">City</label>
                            <input v-model="user_local.city" type="text" class="form-control" name="city" id="city">
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="region">Province</label>
                                <select class="form-control" name="region" id="region">
                                    <option v-bind:key="region" v-for="region in regions">{{ region }}</option>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="postal_code">Postal Code</label>
                                <input v-model="user_local.postal_code" type="text" class="form-control" name="postal_code"
                                       id="postal_code">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="school">Current School</label>
                        <select class="form-control" name="school" id="school">
                            <option v-bind:key="school.id" v-for="school in schools">{{ school }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="professional_certificate_bc">BC Certificate</label>
                        <input type="text" class="form-control" name="professional_certificate_bc"
                               v-model="user_local.professional_certificate_bc" id="professional_certificate_bc">
                    </div>
                    <div class="form-group col">
                        <label for="professional_certificate_yk">YK Certificate</label>
                        <input type="text" class="form-control" name="professional_certificate_yk"
                               v-model="user_local.professional_certificate_yk" id="professional_certificate_yk">
                    </div>
                    <div class="form-group col">
                        <label for="professional_certificate_other">Other</label>
                        <input type="text" class="form-control" name="professional_certificate_other"
                               v-model="user_local.professional_certificate_other" id="professional_certificate_other">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col">
                        <button class="btn btn-danger btn-block" v-on:click.prevent="cancelProfile">Cancel</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block" v-on:click.prevent="saveProfile">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        props: {
            user: {},
            schools: {},
            regions: {}
        },
        data() {
            return {
                user_local: this.user
            }
        },
        mounted() {
        },
        methods: {
            closeModal() {
                this.$modal.hide('profile_form');
            },
            cancelProfile() {
                this.closeModal()
            },
            saveProfile() {
                this.closeModal()
                console.log('saving profile')

                var form = this

                axios.post('/Dashboard/profile', {
                    email: form.user_local.email,
                    preferred_first_name: form.user_local.preferred_first_name,
                    first_name: form.user_local.first_name,
                    last_name: form.user_local.last_name,
                    social_insurance_no: form.user_local.social_insurance_no,
                    professional_certificate_bc: form.user_local.professional_certificate_bc,
                    professional_certificate_yk: form.user_local.professional_certificate_yk,
                    professional_certificate_other: form.user_local.professional_certificate_other,
                    current_school: form.user_local.current_school,
                    address_1: form.user_local.address_1,
                    address_2: form.user_local.address_2,
                    city: form.user_local.city,
                    region: form.user_local.region,
                    postal_code: form.user_local.postal_code
                })
                    .then(function (response) {
                        Event.fire('profile-updated', response.data)
                        console.log('Success!')
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                    });
            }
        }
    }
</script>

<style scoped>

</style>