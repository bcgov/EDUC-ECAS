<template>
    <div class="card contracts-div">
      <div class="card-header contracts-header">
        <h2 class="mt-1 ml-2">My Contracts</h2>
        <button type="button" class="btn btn-outline-primary" @click="showHelp()">
          Help
        </button>
      </div>
      <div class="card-body contracts-body">
        <button class="btn btn-link" @click="expandAll()">
          Expand all
        </button>
        <button class="btn btn-link" @click="collapseAll()">
          Collapse all
        </button>
        <div id="accordion" class="ml-2">
          <div class="card">
            <div class="card-header" id="actionRequiredSectionHeader" @click="toggleActionRequired()">
              <h5 class="pt-1">
                  Action Required
              </h5>
              <p class="pt-3 mb-0">Download, sign, and upload your contract(s) for the following sessions</p>
            </div>

            <div id="actionRequiredSectionBody" v-show="actionRequiredDisplayed">
              <div class="card-body">
                <table class="table table-sm table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Dates</th>
                      <th scope="col">Type</th>
                      <th scope="col">Download Contract</th>
                      <th scope="col">Upload Contract</th>
                      <th scope="col">Uploaded Files</th>
                      <th scope="col">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>May 3 ~ May 5, 2021</td>
                      <td>Foundation Skills Assessment Reading English, Grade 4</td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-download" alt="Download file" style="font-size: 32px; color: #003366;"  />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-upload" alt="Upload file" style="font-size: 32px; color: #f5a742;" />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon :icon="['far','file']" alt="List of uploaded files" style="font-size: 32px;" />
                        </div>
                      </td>
                      <td>
                        <button class="btn btn-block btn-primary" @click="collapseAll()">
                          Submit for review
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card mt-2">
            <div class="card-header" id="pendingReviewedSectionHeader" @click="togglePendingReview()">
              <h5 class="pt-1">
                  Pending Reviewed
              </h5>
              <p class="pt-3 mb-0">Your uploaded contract(s) are being reviewed and you will be notified by email if any follow-up is required.</p>
            </div>
            <div id="pendingReviewedSectionBody" v-show="pendingReviewDisplayed">
              <div class="card-body">
                <table class="table table-sm table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Dates</th>
                      <th scope="col">Type</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>May 3 ~ May 5, 2021</td>
                      <td colspan="5">Foundation Skills Assessment Reading English, Grade 4</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card mt-2">
            <div class="card-header" id="finalizedSectionHeader" @click="toggleFinalized()">
              <h5 class="pt-1">
                  Finalized
              </h5>
              <p class="pt-3 mb-0">Your uploaded contract(s) are finalized.</p>
            </div>
            <div id="finalizedSectionBody" v-show="finalizedDisplayed">
              <div class="card-body">
                <table class="table table-sm table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Dates</th>
                      <th scope="col">Type</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                      <th scope="col">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>May 3 ~ May 5, 2021</td>
                      <td colspan="5">Foundation Skills Assessment Reading English, Grade 4</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <modal name="help_form" height="auto" :scrollable="false" :clickToClose="true">
         <div class="help-div">
          <div class="help-header">
            Please contact us at:
          </div>
          <div class="help-body">
            Please email questions or concerns to exams@gov.bc.ca
          </div>
           
        </div> 
      </modal>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'

export default {
    name: "Contracts",
    computed: {
        ...mapGetters([
            'getUser',
            'getSessions'
        ]),
    },
    data() {
      return {
        contracts: [],
        actionRequiredDisplayed: true,
        pendingReviewDisplayed: false,
        finalizedDisplayed: false,
      }
    },
    mounted() {
      console.log('My Contracts Mounted')

      this.contracts = this.getSessions.map(s => s.assignment.contract_stage).filter(item => !!item);
      console.log(`${this.contracts.length} loaded for contracts.`);
    },
    methods: {
      toggleActionRequired() {
        this.actionRequiredDisplayed = !this.actionRequiredDisplayed;
      },
      togglePendingReview() {
        this.pendingReviewDisplayed = !this.pendingReviewDisplayed;
      },
      toggleFinalized() {
        this.finalizedDisplayed = !this.finalizedDisplayed;
      },
      expandAll() {
        this.actionRequiredDisplayed = true;
        this.pendingReviewDisplayed = true;
        this.finalizedDisplayed = true;
      },
      collapseAll() {
        this.actionRequiredDisplayed = false;
        this.pendingReviewDisplayed = false;
        this.finalizedDisplayed = false;
      },
      showHelp() {
         this.$modal.show('help_form');
      },
    }
}
</script>

<style scoped>
.contracts-div {
  display: flex;
  flex-direction: column;
}

.contracts-header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: ceter;
  height: 75px;
  padding: 16px 12px;
}

.contracts-body {
  padding: 4px;
}

#accordion .card-header:hover {
  background-color: #d1d0d0;
  cursor: pointer;
}

.help-div {
  width: 100%;
}

.help-header {
  height: 75px;
  font-size: 1.35rem;
  font-weight: bold;
  color: red;
  background-color: lightgray;
  padding-top: 16px;
  padding-left: 12px;
}

.help-body {
  height: 125px;
  font-size: 1.25rem;
  padding-top: 30px;
  padding-left: 12px;
}

.icon-box {
  padding-top: 4px;
  padding-right: 4px;
  text-align: center;
  /* height: 45px; */
}


</style>
