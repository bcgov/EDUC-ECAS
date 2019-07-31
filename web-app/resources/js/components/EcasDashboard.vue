<template>

    <div>
        <div>
            <ecas-logout></ecas-logout>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <ecas-profile
                                :data="data.user"
                                @edit-profile-request="showProfile"
                        >

                        </ecas-profile>
                    </div>
                    <profile-credentials :data="data" ></profile-credentials>
                </div>
                <div class="col">
                    <div class="row pt-4">
                        <marking-sessions :data="data" ></marking-sessions>
                    </div>
                </div>
            </div>
        </div>

        <modal name="profile_form" height="auto" :scrollable="true" :clickToClose="false">
            <profile
                    :user="local_data.user"
                    :schools="data.schools"
                    :regions="data.regions"
                    :districts="data.districts"
                    dusk="profile-component"
                    :new_user="newUser"
                    @profile-updated="updateProfile"
            ></profile>
        </modal>

    </div>

</template>

<script>

    export default {
        name: "EcasDashboard",

        props: {
            data: {},
        },
        data() {
            return {
                local_data: this.data,
                isMounted:  false
            }
        },
        mounted() {
            console.log('Ecas Profile Mounted');

            if(this.newUser) {
                this.$modal.show('profile_form');
            }


            this.isMounted = true;

        },

        computed: {

            newUser() {
                return (this.data.user.id === null);
            }

        },

        methods: {

            showProfile() {
                this.$modal.show('profile_form');
            },

            updateProfile(payload) {
                // We must have a valid user now
                console.log('updateProfile method', payload[0].data);
                this.local_data.user = payload[0].data;
            },

        }

    }
</script>