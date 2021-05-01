<template>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-6">
                            <h2>Marking Sessions</h2>
                        </div>
                        <div class="col-6 text-right">
                            <button class="btn btn-link btn-sm " v-text="showPastSessionText" @click="showPastSessions = ! showPastSessions"></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            Click on a session below to apply, accept or decline
                        </div>
                        <div class="col-6">

                            <ul class="nav nav-tabs justify-content-end pt-2">
                                <li class="nav-item mb-0">
                                    <a @click="filter = ''"
                                        class="nav-link"
                                        :class="{ 'active': filter === '' }">All
                                        <span class="badge badge-pill badge-primary">{{ dateFilteredSessions.length }}</span></a>
                                </li>
                                <li v-for="status in uniqueSessionStatus" class="nav-item mb-0">
                                    <a @click="filter = status"
                                        class="nav-link"
                                        :class="{ 'active': filter === status }">{{ status }}
                                        <span class="badge badge-pill badge-primary">{{ countStatus(status) }}</span></a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div class="filter-box">
                        <div class="filter-col-1">
                            <div class="filter-input-box col-sm-4">
                                <font-awesome-icon icon="search" alt="Filter by Type" style="margin-top: 7px; margin-right: 5px; font-size: 18px;"/>
                                <input v-model="typeInputText" v-on:keyup.enter="handleTypeInputText" v-on:blur="handleTypeInputText"
                                    type="text" class="form-control form-control-sm" name="filter-type-input"
                                    id="filter-type-input" placeholder="Filter by Type" size="80">
                            </div>
                            <div class="chips-box col-sm-8">
                                <div v-for="(filteredText, index) in typeFilteredTexts" :key="index" class="chip">
                                    <!-- <div class="chip-head">{{filteredText.charAt(0).toUpperCase()}}</div> -->
                                    <div class="chip-content">{{filteredText}}</div>
                                    <div class="chip-close" @click="deleteTypeChip(filteredText)">
                                        <svg class="chip-svg" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path></svg>
                                    </div>
                                </div>
                            </div>  
                        </div>
                        <div class="filter-col-2">
                            <div class="filter-input-box col-sm-6">
                                <font-awesome-icon icon="search-location" alt="Filter by Location" style="margin-top: 7px; margin-right: 5px; font-size: 22px;"/>
                                <input v-model="locationInputText" v-on:keyup.enter="handleLocationInputText" v-on:blur="handleLocationInputText"
                                    type="text" class="form-control form-control-sm" name="location-type-input"
                                    id="location-type-input" placeholder="Filter by Location" size="80">
                            </div>
                            <div class="chips-box col-sm-6">
                                <div v-for="(filteredText, index) in locationFilteredTexts" :key="index" class="chip">
                                    <!-- <div class="chip-head">{{filteredText.charAt(0).toUpperCase()}}</div> -->
                                    <div class="chip-content">{{filteredText}}</div>
                                    <div class="chip-close" @click="deleteLocationChip(filteredText)">
                                        <svg class="chip-svg" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-hover" :key="sessionsUpdatedCount">
                        <tr>
                            <th :class="[ sortValue('Type')? 'headerSortUp' : 'headerSortDown']" @click="handleSort('Type')">Type</th>
                            <th :class="[ sortValue('Activity')? 'headerSortUp' : 'headerSortDown']" @click="handleSort('Activity')">Activity</th>
                            <th :class="[ sortValue('Dates')? 'headerSortUp' : 'headerSortDown']" @click="handleSort('Dates')">Dates</th>
                            <th :class="[ sortValue('Location')? 'headerSortUp' : 'headerSortDown']" @click="handleSort('Location')">Location</th>
                            <th :class="[ sortValue('Status')? 'headerSortUp' : 'headerSortDown']" @click="handleSort('Status')">Status</th>
                        </tr>
                        <tbody>
                        <tr @click="viewSession(session)"
                            v-for="session in sessionsByFilterAndSort()">
                            <td>{{ session.type.name }}</td>
                            <td>{{ session.activity.name }}</td>
                            <td nowrap>{{ session.date }}</td>
                            <td>{{ session.location }}</td>
                            <td>{{ sessionStatus(session.assignment) }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <modal name="session_form" height="auto">
            <session :session="current_session"></session>
        </modal>
    </div>

</template>

<script>
    export default {
        name: "MarkingSessions",
        props: {
            sessions: null,
        },
        data() {
            return {
                initialSessions: [],
                showPastSessions: false,

                filter: '',
                current_session: {},

                working: false,

                typeInputText: '',
                typeFilteredTexts: [],

                locationInputText: '',
                locationFilteredTexts: [],

                sortData: [
                    { column: 'Type', ascending: true, active: false },
                    { column: 'Activity', ascending: true, active: false },    
                    { column: 'Dates', ascending: true, active: false },
                    { column: 'Location', ascending: true, active: false },
                    { column: 'Status', ascending: true, active: false },
                ],

                sessionsUpdatedCount: 0,

            }
        },
        created() {
            this.initialSessions = this.sessions;
            this.loadSessionsData();
        },
        mounted() {
            console.log('MarkingSessions Mounted');
            Event.listen('session_status_updated', this.updateSessionStatus);
        },
        watch: {
            sessions(newValue) {
                console.log('new sessions as props - reload sessions for marking table');
                this.initialSessions = newValue;
                this.loadSessionsData();
            }
        },
        computed: {
            dateFilteredSessions() {

                if(this.showPastSessions) {
                    // display all sessions
                    return this.initialSessions;
                }

                return this.initialSessions.filter( function (session) {

                    return ! session.isPast;

                });
            },

            sortSessionByEndDate() {

                // Sessions sorted so that the newest sessions are at the bottom
                return this.dateFilteredSessions.sort(function (b, a) {
					return parseInt(b.diff_in_days) - parseInt(a.diff_in_days);
				})
            },

            filteredSessions() {
                var dashboard = this;
                return this.sortSessionByEndDate.filter(function (session) {
                    if (dashboard.filter.length === 0) {
                        return true
                    }
                    return session.assignment.status.name === dashboard.filter
                })
            },

            uniqueSessionStatus() {
                let array =  [...new Set(this.dateFilteredSessions.map(session => session.assignment.status.name))];

                return array.sort();

            },

            showPastSessionText() {
                if(this.showPastSessions) {
                    return 'Show current sessions';
                }

                return 'Show current and past sessions';
            }
        },
        methods: {
            loadSessionsData() {
                // The Ministry business rules require that Sessions on the portal be shown only if the
                // session is published and "Open" OR the user has been assignment related to the session.
                this.initialSessions = this.initialSessions.filter( function (session) {
                     if(session.assignment.status.name === 'Selected') {
                        session.assignment.status.name = "Applied"
                     }
                    return (session.is_published && session.session_status === "Open") || session.assignment.id !== 0;
                });

                this.sessionsUpdatedCount += 1;
            },

            countStatus(status) {
                // var status
                return Object.values(this.dateFilteredSessions).filter(function (session) {
                    return session.assignment.status.name === status
                }).length
            },

            sessionStatus(assignment) {
                switch (assignment.status.name) {
                    case 'Applied':
                        return 'Applied'
                     case 'Invited':
                        return 'Accept Invitation'
                    case 'Accepted':
                        return 'Accepted'
                    case 'Contract':
                        return 'Contract Pending'
                    case 'Confirmed':
                        return 'Contract Received'
                    case 'Declined':
                        return 'Declined'
                    case 'Withdrew':
                        return 'Withdrew'
		            case 'Attendance Recorded':
                        return 'Attended'
                    case 'Completed':
                        return 'Closed'
                }
                return 'Open'
            },

            viewSession(session) {
                console.log('View Session');
                this.current_session = session;
                this.$modal.show('session_form');
            },
            closeModal() {
                this.$modal.hide('session_form');
            },

            updateSessionStatus(response) {
                console.log('updateSessionStatus', response);
                this.$store.commit('UPDATE_SESSION_STATUS', response);
            },

            handleTypeInputText(e) {
                if (!!e.target.value) {
                    this.typeFilteredTexts.push(e.target.value);
                }
                this.typeInputText = '';
            },

            handleLocationInputText(e) {
                if (!!e.target.value) {
                    this.locationFilteredTexts.push(e.target.value);
                }
                this.locationInputText = '';
            },

            deleteTypeChip(selectedText) {
                this.typeFilteredTexts = this.typeFilteredTexts.filter(i => i !== selectedText);
            },

            deleteLocationChip(selectedText) {
                this.locationFilteredTexts = this.locationFilteredTexts.filter(i => i !== selectedText);
            },

            sessionsByFilterAndSort() {
                // Filter
                let results = this.typeFilteredTexts.length === 0? this.filteredSessions : this.filteredSessions.filter(i => this.matchTypeFilteredTexts(i.type.name));
                results = this.locationFilteredTexts.length === 0? results : results.filter(i => this.matchLocationFilteredTexts(i.location));

                // Sort
                const sortOption = this.sortData.find(i => i.active);
                if (!!sortOption) {
                    results = this.sort(sortOption.column, sortOption.ascending, results);
                }

                return results;
            },

            matchTypeFilteredTexts(text) {
                let found = 0;
                this.typeFilteredTexts.forEach(i => {
                    if (text.toLowerCase().includes(i.toLowerCase())) {
                        found++;
                    }
                });
                // AND condition
                return found === this.typeFilteredTexts.length;
            },

            matchLocationFilteredTexts(text) {
                let found = 0;
                this.locationFilteredTexts.forEach(i => {
                    if (text.toLowerCase().includes(i.toLowerCase())) {
                        found++;
                    }
                });
                 // AND condition
                return found === this.locationFilteredTexts.length;
            },

            handleSort(fieldName) {
                this.sortData.forEach(i => {
                    if (i.column === fieldName) {
                        i.active = true;
                        i.ascending = !i.ascending;
                    } else {
                        i.active = false;
                        i.ascending = true;
                    }
                });
            },

            sortValue(fieldName) {
                return this.sortData.find(i => i.column === fieldName).ascending;
            },

            sort(fieldName, ascending, list) {
                let results = list;
                if (fieldName === 'Dates') {
                    results = list.sort(function (a, b) {
					    return ascending? parseInt(a.diff_in_days) - parseInt(b.diff_in_days) : parseInt(b.diff_in_days) - parseInt(a.diff_in_days);
				    })
                } else {
                    results = list.sort((a,b) => {
                        const upperA = this.findValue(a, fieldName).toUpperCase();
                        const upperB = this.findValue(b, fieldName).toUpperCase();
                        if (upperA < upperB) {
                            return ascending? -1 : 1;
                        }
                        if (upperA > upperB) {
                            return ascending? 1 : -1;
                        }

                        return 0;
                    });
                }
                return results;
            },

            findValue(record, fieldName) {
                let val = '';
                switch(fieldName) {
                    case 'Type': 
                        val = record.type.name;
                        break;
                    case 'Activity':
                        val = record.activity.name;
                        break;
                    case 'Location':
                        val = record.location;
                        break;
                    case 'Status':
                        val = this.sessionStatus(record.assignment);
                        break;
                    default:
                        break;

                }
                return val;
            }

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
.filter-box {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 100%;
    margin-top: -10px;
    margin-bottom: 10px;
}
.filter-col-1 {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 60%;
}
.filter-col-2 {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    width: 40%;
}
.filter-input-box {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
}
.chips-box {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    width: 100%;
    margin-top: -4px;  
}
.chip {
    display: inline-flex;
    flex-direction: row;
    background-color: #e5e5e5;
    border: none;
    cursor: default;
    height: 36px;
    outline: none;
    padding: 0;
    margin-right: 4px;
    font-size: 14px;
    color: #333333;
    font-family:"Open Sans", sans-serif;
    white-space: nowrap;
    align-items: center;
    border-radius: 16px;
    vertical-align: middle;
    text-decoration: none;
    justify-content: center;
}
.chip-head {
    display: flex;
    position: relative;
    overflow: hidden;
    background-color: #32C5D2;
    font-size: 1.25rem;
    flex-shrink: 0;
    align-items: center;
    user-select: none;
    border-radius: 50%;
    justify-content: center;
    width: 36px;
    color: #fff;
    height: 36px;
    font-size: 20px;
    margin-right: -4px;
}
.chip-content {
    cursor: inherit;
    display: flex;
    align-items: center;
    user-select: none;
    white-space: nowrap;
    padding-left: 12px;
    padding-right: 12px;
}
.chip-svg {
    color: #999999;
    cursor: pointer;
    height: auto;
    margin: 4px 4px 0 -8px;
    fill: currentColor;
    width: 1em;
    height: 1em;
    display: inline-block;
    font-size: 24px;
    transition: fill 200ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
    user-select: none;
    flex-shrink: 0;
}
.chip-svg:hover {
    color: #666666;
}
.headerSortDown:after,
.headerSortUp:after{
    content: ' ';
    position: relative;
    left: 4px;
    border: 8px solid transparent;
}
.headerSortDown:after{
    top: 14px;
    border-top-color: silver;
}
.headerSortUp:after{
    bottom: 12px;
    border-bottom-color: silver;
}
.headerSortDown,
.headerSortUp{
    padding-right: 10px;
}
</style>
