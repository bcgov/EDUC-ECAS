<template>
    <div class="card">
        <div class="card-header"><h1>Teacher Profile</h1></div>
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
                        <label for="first_name">First Name</label>
                        <input v-model="user_local.first_name" type="text" class="form-control" name="first_name"
                               id="first_name">
                        <form-error :errors="errors" field="first_name"></form-error>
                    </div>
                    <div class="form-group col">
                        <label for="last_name">Last Name</label>
                        <input v-model="user_local.last_name" type="text" class="form-control" name="last_name"
                               id="last_name">
                        <form-error :errors="errors" field="last_name"></form-error>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="email">email</label>
                        <input v-model="user_local.email"
                               type="email"
                               class="form-control"
                               name="email"
                               id="email"
                                required>
                        <form-error :errors="errors" field="email"></form-error>
                    </div>
                    <div class="form-group col">
                        <label for="phone">Phone</label>
                        <input v-model="user_local.phone"
                               type="tel"
                               class="form-control"
                               name="phone"
                               id="phone">
                        <form-error :errors="errors" field="phone"></form-error>
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
                            <form-error :errors="errors" field="address_1"></form-error>
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
                            <form-error :errors="errors" field="city"></form-error>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="region">Province</label>
                                <select class="form-control" v-model="user_local.region" id="region" name="region">
                                    <option v-bind:value="region.code" v-for="region in regions">{{ region.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="postal_code">Postal Code</label>
                                <input v-model="user_local.postal_code" type="text" class="form-control" name="postal_code"
                                       id="postal_code">
                                <form-error :errors="errors" field="postal_code"></form-error>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="district">Current District</label>
                        <select class="form-control" v-model="user_local.district" id="district">
                            <option disabled value="">Please select one</option>
                            <option v-bind:value="district.id" v-for="district in districts">{{ district.name }}</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="school">Current School</label>
                        <select class="form-control" v-model="user_local.school" id="school">
                            <option disabled value="">Please select one</option>
                            <option v-bind:value="school.id" v-for="school in schools">{{ school.name }}</option>
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
                <div class="form-row">
                    <div class="form-group col">
                        <label for="payment">Please pay me by...</label>
                        <select class="form-control" name="payment" id="payment">
                            <option v-bind:key="payment.id" v-for="payment in payments">{{ payment.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col" v-show="showCancel">
                        <button class="btn btn-danger btn-block" v-on:click.prevent="cancelProfile">Cancel</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block" v-on:click.prevent="saveProfile" dusk="save">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import FormError from './FormError.vue';

    export default {
        name: "Profile",
        props: {
            user: {},
            schools: {},
            regions: {},
            districts: {},
            payments: {},
            new_user: false
        },
        components: {
            FormError,
        },
        data() {
            return {
                user_local: {...this.user},
                errors: {}
            }
        },
        mounted() {
        },
        computed: {
            showCancel() {
                return !this.new_user
            }
        },
        methods: {
            closeModal() {
                this.$modal.hide('profile_form');
            },
            cancelProfile() {
                this.closeModal()
            },
            saveProfile() {

                console.log('saving profile')

                var form = this

                axios.post('/Dashboard/profile', {
                    email: form.user_local.email,
                    phone: form.user_local.phone,
                    preferred_first_name: form.user_local.preferred_first_name,
                    first_name: form.user_local.first_name,
                    last_name: form.user_local.last_name,
                    social_insurance_no: form.user_local.social_insurance_no,
                    professional_certificate_bc: form.user_local.professional_certificate_bc,
                    professional_certificate_yk: form.user_local.professional_certificate_yk,
                    professional_certificate_other: form.user_local.professional_certificate_other,
                    school: form.user_local.school,
                    address_1: form.user_local.address_1,
                    address_2: form.user_local.address_2,
                    city: form.user_local.city,
                    region: form.user_local.region,
                    postal_code: form.user_local.postal_code,
                    payment: form.user_local.payment,
                    district: form.user_local.district
                })
                    .then(function (response) {
                        console.log('Success!')
                        form.closeModal()
                        Event.fire('profile-updated', response.data)
                    })
                    .catch(function (error) {
                        console.log('Failure!')
                        if (error.response.status == 422){
                            form.errors = error.response.data.errors;
                        }
                    });
            }
        }
    }
</script>

<style scoped>

</style>