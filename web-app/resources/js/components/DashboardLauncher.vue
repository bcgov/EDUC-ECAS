<template>
    <div v-if="isLoaded">
        <dashboard-component
                :user="dashboard.user"
                :user_credentials="dashboard.user_credentials"
                :sessions="dashboard.sessions"
                :subjects="dashboard.subjects"
                :regions="dashboard.regions"
                :countries="dashboard.countries"
                :credentials="dashboard.credentials" >
        </dashboard-component>
    </div>
</template>

<script>

    import DashboardComponent from './DashboardComponent';

    export default {
        data() {
            return {
                dashboard: null,
                isLoaded: false
            }
        },

        mounted() {

            console.log('Component mounted.');

            axios.get('/api/dashboard')
                .then( response => {
                    console.log('api returned:  ', response.data  );
                    this.dashboard = response.data;
                    this.isLoaded = true;

                })
                .catch( error => {
                    console.log('Fail!', error);
                });

        },

        components: {
            DashboardComponent
        }
    }
</script>
