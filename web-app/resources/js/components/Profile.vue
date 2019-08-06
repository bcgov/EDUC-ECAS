<template>
    <div class="card">
        <div class="card-header"><h1>Teacher Profile</h1></div>
        <div class="card-body">
            <form>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="first_name" class="required">First Name</label>
                        <input v-model="user_local.first_name" type="text" class="form-control" name="first_name"
                               id="first_name">
                        <form-error :errors="errors" field="first_name"></form-error>
                    </div>
                    <div class="form-group col">
                        <label for="preferred_first_name">Preferred First</label>
                        <input v-model="user_local.preferred_first_name"
                               type="text" class="form-control"
                               name="preferred_first_name" id="preferred_first_name">
                    </div>
                    <div class="form-group col">
                        <label for="last_name" class="required">Last Name</label>
                        <input v-model="user_local.last_name" type="text" class="form-control" name="last_name"
                               id="last_name">
                        <form-error :errors="errors" field="last_name"></form-error>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col">
                        <label for="email" class="required">Email</label>
                        <input v-model="user_local.email"
                               type="email"
                               class="form-control"
                               name="email"
                               id="email"
                                required>
                        <form-error :errors="errors" field="email"></form-error>
                    </div>
                    <div class="form-group col">
                        <label for="phone" class="required">Phone</label>
                        <input v-model="user_local.phone"
                               type="tel"
                               class="form-control"
                               name="phone"
                               id="phone">
                        <form-error :errors="errors" field="phone"></form-error>
                    </div>
                    <div class="form-group col">
                        <label for="social_insurance_number">Social Insurance Number</label>
                        <input v-if=" ! user_local.is_SIN_on_file" v-model="user_local.social_insurance_number" type="text" class="form-control"
                               name="social_insurance_number" id="social_insurance_number">
                        <input v-if="user_local.is_SIN_on_file" disabled type="text" class="form-control"
                               name="social_insurance_number" placeholder="Received - thank you">
                    </div>
                </div>
                <div class="form-row">
                    <div class="col">
                        <div class="form-group">
                            <label for="address_1" class="required">Home Address</label>
                            <input v-model="user_local.address_1" type="text" class="form-control" name="address_1"
                                   id="address_1">
                            <form-error :errors="errors" field="address_1"></form-error>
                        </div>
                        <div class="form-group">
                            <label for="address_2">Home Address Line 2</label>
                            <input v-model="user_local.address_2" type="text" class="form-control" name="address_2"
                                   id="address_2">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="city" class="required">City</label>
                            <input v-model="user_local.city" type="text" class="form-control" name="city" id="city">
                            <form-error :errors="errors" field="city"></form-error>
                        </div>
                        <div class="form-row">
                            <div class="form-group col">
                                <label for="region">Province</label>
                                <select class="form-control" v-model="user_local.region" id="region" name="region">
                                    <option :value="region" v-for="region in regions">{{ region.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col">
                                <label for="postal_code" class="required">Postal Code</label>
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
                        <vue-bootstrap-typeahead
                                v-model="districtQuery"
                                :serializer="s => s.name"
                                :data="schools"
                                id="district"
                                :placeholder="districtPlacholder"
                                @hit="user_local.district = $event"
                        />
                    </div>
                    <div class="form-group col">
                        <label for="school">Current School</label>
                        <vue-bootstrap-typeahead
                                v-model="schoolQuery"
                                :serializer="s => s.name"
                                :data="schools"
                                id="school"
                                :placeholder="schoolPlacholder"
                                @hit="user_local.school = $event"
                        />
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
                    <div class="col" v-show="showCancel">
                        <button class="btn btn-danger btn-block" v-on:click.prevent="cancelProfile">Cancel</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-primary btn-block" v-on:click.prevent="saveProfile" dusk="save">
                            <span>
                                <div class="loader text-center" v-show="working"></div>
                            </span>
                            <div v-show="!working">Save</div>
                        </button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">* Required</div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import FormError from './FormError.vue';
    import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';

    export default {
        name: "Profile",
        props: {
            user: {},
            regions: {},
            new_user: false
        },
        components: {
            FormError,
            VueBootstrapTypeahead
        },
        data() {
            return {
                user_local:     {...this.user},
                errors:         {},
                working:        false,
                schools:        [],
                schoolQuery:    '',
                districts:      [],
                districtQuery:  ''
            }
        },
        mounted() {
            if (this.new_user) {
                this.user_local.region.id = 'BC'
            }
        },
        computed: {
            schoolPlacholder() {
              if(this.user_local.school) {
                  return this.user_local.school.name;
              }

              return 'Search by school name ...';

            },
            districtPlacholder() {
                if(this.user_local.district) {
                    return this.user_local.district.name;
                }

                return 'Search by district name ...';

            },
            showCancel() {
                return !this.new_user
            }
        },
        watch: {
            schoolQuery: _.debounce(function(school) { this.getSchoolNames(school) }, 500),
            districtQuery: _.debounce(function(district) { this.getDistrictNames(district) }, 500),
        },
        methods: {
            async getSchoolNames(query) {
                const res = await fetch('/api/schools?q=:query'.replace(':query', query));
                const suggestions = await res.json();
                console.log('schools', suggestions);
                this.schools = suggestions.data
            },
            async getDistrictNames(query) {
                const res = await fetch('/api/districts?q=:query'.replace(':query', query));
                const suggestions = await res.json();
                console.log('districts', suggestions);
                this.schools = suggestions.data
            },
            closeModal() {
                this.$modal.hide('profile_form');
            },
            cancelProfile() {
                this.closeModal()
            },
            saveProfile() {

                console.log('saving profile')

                if (this.working) {
                    return
                }

                this.working = true;

                var form = this

                // Data must be registered here to interact with the form
                var data = {
                    id: form.user_local.id,
                    email: form.user_local.email,
                    phone: form.user_local.phone,
                    preferred_first_name: form.user_local.preferred_first_name,
                    first_name: form.user_local.first_name,
                    last_name: form.user_local.last_name,
                    social_insurance_number: form.user_local.social_insurance_number,
                    professional_certificate_bc: form.user_local.professional_certificate_bc,
                    professional_certificate_yk: form.user_local.professional_certificate_yk,
                    professional_certificate_other: form.user_local.professional_certificate_other,
                    address_1: form.user_local.address_1,
                    address_2: form.user_local.address_2,
                    city: form.user_local.city,
                    region: form.user_local.region.id,
                    postal_code: form.user_local.postal_code,
                    school_id: form.user_local.school.id,
                    district_id: form.user_local.district.id,
                };

                if (this.new_user) {
                    axios.post('/api/profiles', data)
                        .then(function (response) {
                            console.log('Create Profile'. data.school_id);
                            form.closeModal();
                            form.working = false;
                            Event.fire('profile-updated', response.data)
                        })
                        .catch(function (error) {
                            console.log('Failure!', data);
                            form.working = false;
                            if (error.response.status === 422){
                                form.errors = error.response.data.errors;
                            }
                        });
                }
                else {
                    axios.patch('/api/profiles/' + this.user.id, data)
                        .then(function (response) {
                            console.log('Patch Profile', data.school_id);
                            form.working = false;
                            form.closeModal();
                            Event.fire('profile-updated', response.data)
                        })
                        .catch(function (error) {
                            console.log('Failure!');
                            form.working = false;
                            if (error.response.status === 422){
                                form.errors = error.response.data.errors;
                            }
                        });
                }
            }
        }
    }
</script>

<style scoped>
    .required:after { content:" *"; }
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