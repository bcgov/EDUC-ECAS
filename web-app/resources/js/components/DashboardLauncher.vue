<template>
    <div>

        <div class="dashboard-spinner text-center" v-if="!isLoaded"></div>

        <div v-if="isLoaded">
            <dashboard-component
                    :user="dashboard.user"
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
                isLoaded: false,
            }
        },

        mounted() {
            console.log('Component mounted.');
            this.performLoadDashboardData();
        },

        components: {
            DashboardComponent
        },

        methods: {
            loadDashboardData() {
                return axios.get('/api/dashboard')
                    .then( response => {
                        console.log('dashboard api returned:  ', response.data  );
                        this.dashboard = response.data;
                    })
                    .catch( error => {
                        console.log('Fail!', error);
                    });
            },
            async performLoadDashboardData() {
                console.log('dashboard updated: load data - start');
                this.isLoaded = false;
                await this.loadDashboardData();
                this.$store.commit('SET_SESSIONS', this.dashboard.sessions);
                this.$store.commit('SET_CREDENTIALS', this.dashboard.user_credentials);
                this.isLoaded = true;
                console.log('dashboard updated: load data - end');
            }  
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
    .icon-spinner {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 3em;
        height: 3em;
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

