<template>

    <div>
        <div>
            <ecas-logout></ecas-logout>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <ecas-profile :data="data" :new_user="newUser"></ecas-profile>
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

    </div>

</template>

<script>
    import {mapGetters}         from 'vuex';


    export default {
        name: "EcasDashboard",

        props: {
            data: {},
        },
        data() {
            return {
                new_user: false
            }
        },
        mounted() {
            console.log('Ecas Profile Mounted');

            this.$store.commit('SET_USER', this.data.user)
        },
        computed: {
            ...mapGetters([
                'getUser',
            ]),

            newUser() {
                return (this.data.user.id === null)
            }

        }

    }
</script>