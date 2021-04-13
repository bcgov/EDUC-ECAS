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
              <font-awesome-icon v-if="!actionRequiredDisplayed" class="float-right" icon="angle-right" alt="Show contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <font-awesome-icon v-if="actionRequiredDisplayed" class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <h5 class="pt-1">
                  Action Required
              </h5>
              <p class="pt-1 mb-0">Download, sign, and upload your contract(s) for the following sessions</p>
            </div>

            <div id="actionRequiredSectionBody" v-show="actionRequiredDisplayed">
              <div class="card-body">
                <table class="table table-sm table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Dates</th>
                      <th scope="col">Type</th>
                      <th scope="col">Download</th>
                      <th scope="col">Upload</th>
                      <th scope="col">View</th>
                      <th scope="col">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>May 3 ~ May 5, 2021</td>
                      <td>Foundation Skills Assessment Reading English, Grade 4</td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-download" alt="Download file" style="font-size: 32px; color: #003366;" 
                            @click="showFileDownload()" />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-upload" alt="Upload file" style="font-size: 32px; color: #f5a742;" 
                            @click="showFileUpload()" />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon :icon="['far','file']" alt="List of uploaded files" style="font-size: 32px;"
                            @click="showFileUploadedList()" />
                        </div>
                      </td>
                      <td>
                        <div class="button-box">
                          <button class="btn btn-block btn-primary" @click="collapseAll()">
                            Submit for review
                          </button>
                        </div>
                      </td>
                    </tr>
                    <tr v-for="item in getActionRequiredList()" :key="item.id" >
                      <td>May 3 ~ May 5, 2021</td>
                      <td>Foundation Skills Assessment Reading English, Grade 4</td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-download" alt="Download file" style="font-size: 32px; color: #003366;"
                            @click="showFileDownload()" />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon icon="file-upload" alt="Upload file" style="font-size: 32px; color: #f5a742;" 
                            @click="showFileUpload()" />
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <font-awesome-icon :icon="['far','file']" alt="List of uploaded files" style="font-size: 32px;" />
                        </div>
                      </td>
                      <td>
                        <div class="button-box">
                          <button class="btn btn-block btn-primary" @click="collapseAll()">
                            Submit for review
                          </button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card mt-2">
            <div class="card-header" id="pendingReviewedSectionHeader" @click="togglePendingReview()">
              <font-awesome-icon v-if="!pendingReviewDisplayed" class="float-right" icon="angle-right" alt="Show contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <font-awesome-icon v-if="pendingReviewDisplayed" class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <h5 class="pt-1">
                  Pending Review
              </h5>
              <p class="pt-1 mb-0">Your uploaded contract(s) are being reviewed and you will be notified by email if any follow-up is required.</p>
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
              <font-awesome-icon v-if="!finalizedDisplayed" class="float-right" icon="angle-right" alt="Show contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <font-awesome-icon v-if="finalizedDisplayed" class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <h5 class="pt-1">
                  Finalized
              </h5>
              <p class="pt-1 mb-0">Your uploaded contract(s) are finalized.</p>
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
      <modal name="help_form" height="auto" :scrollable="false" :clickToClose="false">
         <div class="card">
          <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeHelp">X</button>
            <h2>Help</h2>
          </div>
          <div class="card-body">
            <p>For assistance, please email questions or concerns to exams@gov.bc.ca</p>
          </div>
        </div> 
      </modal>
      <modal name="file_upload_form" height="auto" :scrollable="false" :clickToClose="false">
         <file-uploader/>
      </modal>
       <modal name="file_uploaded_list" height="auto" :scrollable="false" :clickToClose="false">
         <file-uploaded-list/>
      </modal>
      <modal name="file_download_form" height="auto" :scrollable="false" :clickToClose="false">
         <file-downloader/>
      </modal>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import FileUploader from './FileUploader.vue';
import FileUploadedList from './FileUploadedList.vue'
import FileDownloader from './FileDownloader.vue'

export default {
    name: "Contracts",
    components: {
        FileUploader,
        FileUploadedList,
        FileDownloader
    },
    computed: {
        ...mapGetters([
            'getUser',
            'getSessions',
            'getAssignments',
        ]),
    },
    data() {
      return {
        contracts: [],
        selectedAssignment: null,
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
      closeHelp() {
        this.$modal.hide('help_form');
      },
      showFileUpload() {
        this.$modal.show('file_upload_form');
      },
      showFileUploadedList() {
        this.$modal.show('file_uploaded_list');
      },
      showFileDownload() {
        this.$modal.show('file_download_form');
      },
      getActionRequiredList() {
        if (this.getAssignments && this.getAssignments.length > 0) {
         return this.getAssignments.filter(c => c.statusCode === 'Sent')
        }
        return [];
      },
      getPendingReviewedList() {
        if (this.getAssignments && this.getAssignments.length > 0) {
          return this.getAssignments.filter(c => c.statusCode === 'Contract Submitted')
        }
        return [];
      },
      getFinalizedList() {
        if (this.getAssignments && this.getAssignments.length > 0) {
          return this.getAssignments.filter(c => c.statusCode === 'Signed')
        }
        return [];
      }
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

.icon-box {
  padding-top: 4px;
  padding-right: 4px;
  text-align: center;
  /* height: 45px; */
}

.button-box {
  width: 156px;
}


</style>
