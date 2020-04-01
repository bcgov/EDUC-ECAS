<template>
    <div>

        <div class="dashboard-spinner text-center" v-if="! isLoaded"></div>

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

<style>
    .nav-tabs {
        border-bottom: none;
    }
    .dashboard-spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 5em;
        height: 5em;
        margin: 5em auto auto auto;
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

