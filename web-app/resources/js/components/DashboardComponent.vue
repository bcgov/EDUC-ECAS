<template>
    <div>
        <div class="card">
            <div class="card-body card-body-mobile">
                <div class="row d-flex justify-content-between">
                    <div class="col" v-if="!displayContracts">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col" v-if="displayContracts">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a @click="hideContracts()">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Contracts</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="d-flex justify-content-between mr-3">
                        <div id="dashboard" class="mr-2">
                            <button type="button" class="btn btn-outline-primary" @click="hideContracts" v-if="displayContracts">Dashboard</button>
                        </div>
                        <div id="logout" class="ml-2">
                            <button type="button" class="btn btn-primary" @click="$keycloak.logoutFn" v-if="$keycloak.authenticated">Log out</button>
                        </div>
                    </div>
                </div>
                <div class="row" v-if="!displayContracts">
                    <div class="col pb-3">
                        <div class="card">
                            <div class="card-header">
                                <button @click="toggleMenu" :class="[menuVisible? 'btn-secondary' : 'btn-primary', 'float-right', 'btn']">Menu</button>
                                <h2 class ="h2-mobile">
                                    <span v-if="getUser.preferred_first_name">{{ getUser.preferred_first_name }}</span>
                                    <span v-else>{{ getUser.first_name }}</span>
                                    {{ getUser.last_name }}
                                </h2>
                            </div>
                            <div class="card-body">
                                <ul v-show="menuVisible" class="list-group mt-n3 float-right">
                                    <li class="list-group-item d-flex justify-content-between align-items-center"
                                        @click="showProfile()">
                                        Profile
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"
                                        @click="showCredentials()">
                                        Credentials
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center"
                                        @click="showContracts()">
                                        Contracts
                                    </li>
                                </ul>
                                <p v-show="!new_user">
                                    {{ getUser.email }}<br/>
                                    {{ getUser.address_1 }}<br/>
                                    <span v-if="getUser.address_2">{{ getUser.address_2 }}<br /></span>
                                    {{ getUser.city }}, <span v-if="mounted">{{ getUser.region.id }}</span> {{ getUser.postal_code }}
                                </p>
                                <p v-if="getUser.professional_certificate_bc === 'Yes'">
                                    <strong>BC Professional Certificate:</strong>
                                    <font-awesome-icon icon="check" alt="BC Professional Certificate"/>
                                </p>
                                <p v-if="getUser.professional_certificate_yk === 'Yes'" >
                                    <strong>Yukon Professional Certificate:</strong>
                                    <font-awesome-icon icon="check" alt="Yukon Professional Certificate"/>
                                </p>
                                <p v-if="getUser.district">
                                    <strong>District:</strong> {{ getUser.district.name }}</p>
                                <p v-if="getUser.school">
                                    <strong>School:</strong> {{ getUser.school.name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col pb-3">
                        <div class="dashboard-spinner text-center" v-if="!isContractsLoaded"></div>
                        <div class="card" v-else>
                            <div class="card-header contracts-header">
                                <h2 class ="h2-mobile">My Contracts</h2>
                                <div class="icon-box">
                                    <a data-toggle="tooltip" data-placement="top" title="Help!">
                                    <font-awesome-icon :icon="['far','question-circle']" alt="Help inquiry" style="margin-top: 4px; font-size: 32px; color: #f5a742;"
                                        @click="showHelp()" />
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="contacts-stats-summary-box mt-n2">
                                    <div v-if="checkAssignmentsStatus()" class="alert-notice">
                                        <a @click="showContracts()" data-toggle="tooltip" data-placement="top" title="Manage my contracts">Action Required</a> <span class="badge badge-pill badge-warning ml-n1">{{sent_count}}</span>
                                    </div>
                                    <div v-else>Action Required <span class="badge badge-pill badge-info mt-n3 ml-n1">{{sent_count}}</span></div>
                                    <div>Pending Review <span class="badge badge-pill badge-info mt-n3 ml-n1">{{review_count}}</span></div>
                                    <div >Finalized <span class="badge badge-pill badge-info mt-n3 ml-n1">{{final_count}}</span></div>
                                </div>
                                <div class="my-contracts-box mt-2">
                                    <p class="mt-1">Download and submit your signed contracts for the current session.</p>
                                </div>
                                <div class="btn-group-box mt-n4-custom mt-n4-mobile">
                                    <button class="btn btn-primary" @click="showContracts()">
                                        <span>
                                            <div class="text-center">Manage my contracts</div>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="!displayContracts">
                    <div class="row"  v-if="!isSessionsLoaded">
                        <div class="dashboard-spinner text-center"></div>
                    </div>
                    <marking-sessions v-else :sessions="sessions"></marking-sessions>
                </div>
            </div>
            <div class="row mt-n3" v-if="displayContracts">
                <div class="col">
                    <contracts dusk="contracts-component"></contracts>
                </div>
            </div>
        </div>
        <modal name="profile_form" height="auto" :scrollable="true" :clickToClose="false">
            <profile
                    :user="getUser"
                    :regions="regions"
                    :countries="countries"
                    :new_user="new_user"
                    dusk="profile-component"
            ></profile>
        </modal>
        <modal name="credentials_form" height="auto" :scrollable="true" :clickToClose="false">
            <credentials
                    :user="getUser"
                    :credentials="credentials"
                    dusk="credentials-component"
            ></credentials>
        </modal>
        <modal name="help_form1" height="auto" :scrollable="false" :clickToClose="false">
            <help formName="help_form1"/>
        </modal>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'
    import Profile from './Profile.vue';
    import Credentials from './Credentials.vue';
    import Contracts from './Contracts.vue';
    import MarkingSessions from './MarkingSessions.vue';
    import Help from './Help.vue';

    export default {
        name: "DashboardComponent",

        components: {
            Profile,
            Credentials,
            Contracts,
            MarkingSessions,
            Help
        },

        props: {
            user: {},
            credentials: {},
            subjects: {},
            regions: {},
            countries: {}
        },
        data() {
            return {
                sessions: [],
                isContractsLoaded: false,
                isSessionsLoaded: true,
                filter: '',
                current_session: {},
                sent_count: 0,
                review_count: 0,
                final_count: 0,
                new_user: false,
                mounted: false,
                menuVisible: false,
                displayContracts: false,
            }
        },
        created() {
            this.sessions = this.getSessions.filter(item => !!item);
        },
        mounted() {
            console.log('Dashboard Mounted')

            Event.listen('launch-profile-modal', this.showProfile);
            Event.listen('profile-updated', this.updateProfile);
            Event.listen('refresh-sessions-data', this.refreshSessionsData);
            Event.listen('refresh-contracts-data', this.refreshContractsData);

            this.$store.commit('SET_USER', this.user);

            if ( ! this.user.id) {
                this.new_user = true;
                this.showProfile()
            } else {
                this.assignmentsUpdated();
            }

            this.mounted = true;
        },
        computed: {
            ...mapGetters([
                'getUser',
                'getSessions',
                'getAssignments',
            ]),
        },
        watch: {
            getSessions: {
                deep: true,
                handler() {
                    this.sessions = this.getSessions.filter(item => !!item);
                    console.log(`${this.sessions.length} session(s) re-loaded.`);
                }
            }
        }, 
        methods: {
            toggleMenu() {
                this.menuVisible = !this.menuVisible;
            },
            showContracts() {
                this.displayContracts = true;
            },
            hideContracts() {
                this.displayContracts = false;
            },
            showProfile() {
                this.$modal.show('profile_form');
            },
            showCredentials() {
                this.$modal.show('credentials_form');
            },
            showHelp() {
                this.$modal.show('help_form1');
            },
            updateProfile(user) {
                // We must have a valid user now
                console.log('updateProfile event', user.data.data);
                this.new_user = false;
                this.$store.commit('SET_USER', user.data.data)
            },
            checkAssignmentsStatus() {
                return this.sent_count > 0;
            },
            getAssignmentsStats() {
                let sents = 0;
                let reviews = 0;
                let finals = 0;
                if (this.getAssignments.length > 0) {
                    this.getAssignments.forEach(a => {
                        if (a.EducContractStage === 'Contract Sent') {
                            sents += 1;
                        } else if (a.EducContractStage === 'Contract Submitted') {
                            reviews += 1;
                        } else if (a.EducContractStage === 'Contract Signed') {
                            finals += 1;
                        }
                    });
                    this.sent_count = sents;
                    this.review_count = reviews;
                    this.final_count = finals;
                }
            },
            loadAssignments() {
                return axios.get(`/api/${this.user.id}/portalassignment`)
                .then( response => {
                    //console.log('portal assignments api returned:  ', response.data  );
                    this.$store.commit('SET_ASSIGNMENTS', response.data.PortalAssignment.map(a => ({...a, 
                            isDownloadFileInProgress: false, isUploadedFilesInProgress: false, isSubmitInProgress: false
                        }))
                    );
                })
                .catch( error => {
                    console.log('Fail!', error);
                });
            },
            async assignmentsUpdated() {
                console.log('assignments updated: load data & calculate stats - start');
                this.isContractsLoaded = false;
                await this.loadAssignments();
                this.getAssignmentsStats();
                this.isContractsLoaded = true;
                console.log('assignments updated: load data & calculate stats - end');
            },
            reloadSessionsData() {
                return axios.get('/api/dashboard')
                .then( response => {
                    //console.log('dashboard api: session(s) returned:  ', response.data.sessions);
                    this.$store.commit('SET_SESSIONS', response.data.sessions);
                })
                .catch( error => {
                    console.log('Fail!', error);
                });
            },
            async sessionsUpdated() {
                console.log('sessions updated: load data - start');
                this.isSessionsLoaded = false;
                await this.reloadSessionsData();
                this.isSessionsLoaded = true;
                console.log('sessions updated: load data - end');
            },
            refreshSessionsData() {
                setTimeout(async () => {
					console.log('refreshing sessions data...');
                    await this.sessionsUpdated();
				}, 2000);
            },
            async refreshContractsData() {
                console.log('refreshing contracts data...');
                await this.assignmentsUpdated();
            },
        }

    }
</script>

<style>
    .nav-tabs {
        border-bottom: none;
    }
    .loader {
        border: 4px solid #f3f3f3; /* Light grey */
        border-top: 4px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 16px;
        height: 16px;
        margin: auto;
        animation: spin 2s linear infinite;
    }
    .list-group-item {
        padding: 0.35rem 0.65rem;
    }
    .list-group-item:hover {
        color: #003366;
        background-color: lightgray;
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

<style scoped>
.contracts-header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: ceter;
  /* height: 75px;
  padding: 16px 12px; */
}

.my-contracts-box {
    border-top: 1px #cccbcb solid;
}

.btn-group-box {
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: flex-end;
}

.status-box {
    color: red;
    font-size: 1.15rem;
    font-weight: bold;
    padding: 2px 4px;
}

.contacts-stats-summary-box {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-size: 1.06rem;
}
@media (max-width: 1024px){
    .contacts-stats-summary-box {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    font-size: 1.0rem;
}
}

.alert-notice a {
    font-size: 1.07rem;
    font-weight: bold;
    color: red;
}
@media (max-width: 1024px){
    .alert-notice a {
    font-size: 1.0rem;
    font-weight: bold;
    color: red;
}
}

.alert-notice a:hover {
    text-decoration: underline;
    font-weight: bolder;
    color: #cf0404;
    cursor: pointer;
}

.breadcrumb {
    padding: 0px 2px;
    background-color: white;
}

.breadcrumb a:hover {
    text-decoration: underline;
    cursor: pointer; 
}

.icon-box {
  padding-top: 2px;
  text-align: center;
}

.icon-box:hover {
  cursor: pointer;
}

</style>
