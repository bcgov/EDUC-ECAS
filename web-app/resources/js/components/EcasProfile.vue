<template>

    <div class="card">
        <div class="card-header">
            <button @click="showProfile" class="float-right btn btn-primary">Edit</button>
            <h2>
                <span v-if="getUser.preferred_first_name">{{ getUser.preferred_first_name }}</span>
                <span v-else>{{ getUser.first_name }}</span>
                {{ getUser.last_name }}
            </h2>
        </div>
        <div class="card-body">
            <p v-show=" ! newUser">{{ getUser.email }}<br/>
                {{ getUser.address_1 }}<br/>
                {{ getUser.city }}, {{ getUser.region }} {{ getUser.postal_code }}
            </p>
            <p v-if="getUser.professional_certificate_bc">
                <strong>BC Professional Certificate:</strong> {{ getUser.professional_certificate_bc
                }}
            </p>
            <p v-if="getUser.professional_certificate_yk">
                <strong>Yukon Professional Certificate:</strong> {{
                getUser.professional_certificate_yk }}</p>
            <p v-if="getUser.professional_certificate_other">
                <strong>Other Certificate:</strong> {{ getUser.professional_certificate_other }}</p>
            <p v-if="getUser.district">
                <strong>District:</strong> {{ getUser.district.name }}</p>
            <p v-if="getUser.school">
                <strong>School:</strong> {{ getUser.school.name }}</p>
        </div>

        <modal name="profile_form" height="auto" :scrollable="true" :clickToClose="false">
            <profile
                    :user="getUser"
                    :schools="data.schools"
                    :regions="data.regions"
                    :districts="data.districts"
                    :new_user="newUser"
                    dusk="profile-component"
            ></profile>
        </modal>
    </div>

</template>

<script>
    import {mapGetters}         from 'vuex'


    export default {
        name: "EcasProfile",

        props: {
            data: {},
        },
        data() {
            return {
                working: false
            }
        },
        mounted() {
            console.log('Ecas Profile Mounted');

            this.$store.commit('SET_USER', this.data.user);

            Event.listen('profile-updated', this.updateProfile);

            if (this.newUser) {
                this.showProfile()
            }
        },
        computed: {
            ...mapGetters([
                'getUser',
            ]),

            newUser() {
                return (this.data.user.id === null);
            }

        },
        methods: {

            showProfile() {
                this.$modal.show('profile_form');
            },
            updateProfile(user) {
                // We must have a valid user now
                this.$store.commit('SET_USER', user)
            },

        }

    }
</script>