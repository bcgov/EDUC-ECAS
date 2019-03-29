<template>
    <div class="card">
        <div class="card-header"><h1>Edit Profile</h1></div>
        <div class="card-body">
            <form>
                <div class="form-group row">
                    <label for="email" class="col-4 col-form-label">email</label>
                    <div class="col">
                        <input v-model="user_local.email" type="text" class="form-control" name="email" id="email">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="preferred_first_name" class="col-4 col-form-label">preferred_first_name</label>
                    <div class="col">
                        <input v-model="user_local.preferred_first_name"
                               type="text" class="form-control"
                               name="preferred_first_name" id="preferred_first_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="first_name" class="col-4 col-form-label">first_name</label>
                    <div class="col">
                        <input v-model="user_local.first_name" type="text" class="form-control" name="first_name"
                               id="first_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="last_name" class="col-4 col-form-label">last_name</label>
                    <div class="col">
                        <input v-model="user_local.last_name" type="text" class="form-control" name="last_name"
                               id="last_name">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="social_insurance_no" class="col-4 col-form-label">social_insurance_no</label>
                    <div class="col">
                        <input v-model="user_local.social_insurance_no" type="text" class="form-control"
                               name="social_insurance_no" id="social_insurance_no">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_bc"
                           class="col-4 col-form-label">professional_certificate_bc</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_bc"
                               v-model="user_local.professional_certificate_bc" id="professional_certificate_bc">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_yk"
                           class="col-4 col-form-label">professional_certificate_yk</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_yk"
                               v-model="user_local.professional_certificate_yk" id="professional_certificate_yk">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="professional_certificate_other"
                           class="col-4 col-form-label">professional_certificate_other</label>
                    <div class="col">
                        <input type="text" class="form-control" name="professional_certificate_other"
                               v-model="user_local.professional_certificate_other" id="professional_certificate_other">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="school" class="col-4 col-form-label">current_school</label>
                    <div class="col">
                        <select class="form-control" name="school" id="school">
                            <option v-bind:key="school.id" v-for="school in schools">{{ school }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_1" class="col-4 col-form-label">address_1</label>
                    <div class="col">
                        <input v-model="user_local.address_1" type="text" class="form-control" name="address_1"
                               id="address_1">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_2" class="col-4 col-form-label">address_2</label>
                    <div class="col">
                        <input v-model="user_local.address_2" type="text" class="form-control" name="address_2"
                               id="address_2">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="city" class="col-4 col-form-label">city</label>
                    <div class="col">
                        <input v-model="user_local.city" type="text" class="form-control" name="city" id="city">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="region" class="col-4 col-form-label">region</label>
                    <div class="col">
                        <input v-model="user_local.region" type="text" class="form-control" name="region" id="region">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="postal_code" class="col-4 col-form-label">postal_code</label>
                    <div class="col">
                        <input v-model="user_local.postal_code" type="text" class="form-control" name="postal_code"
                               id="postal_code">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-4"></div>
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
            schools: {}
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