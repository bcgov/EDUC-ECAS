<template>
    <div class="card contracts-div">
      <div class="card-header contracts-header">
        <h2 class="mt-1 ml-2">My Contracts</h2>
        <!-- <button type="button" class="btn btn-outline-primary" @click="showHelp()">
          Help
        </button> -->
        <div class="icon-box">
          <a data-toggle="tooltip" data-placement="top" title="Help!">
          <font-awesome-icon :icon="['far','question-circle']" alt="Help inquiry" style="margin-top: 4px; font-size: 32px; color: #f5a742;"
            @click="showHelp()" />
          </a>
        </div>
      </div>
      <div class="card-body contracts-body">
        <button class="btn btn-link" @click="expandAll()">
          Expand all
        </button>
        <button class="btn btn-link" @click="collapseAll()">
          Collapse all
        </button>
        <div id="accordion" class="mx-1">
          <div class="card">
            <div class="card-header" id="actionRequiredSectionHeader" @click="toggleActionRequired()">
              <font-awesome-icon v-if="!actionRequiredDisplayed" class="float-right" icon="angle-right" alt="Show contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <font-awesome-icon v-else class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <h5 class="pt-1">
                  Action Required
              </h5>
              <p class="pt-1 mb-0">Step 1: Download, sign, and upload your contract(s) for the following sessions. Step 2: Submit.</p>
            </div>

            <div id="actionRequiredSectionBody" v-show="actionRequiredDisplayed">
              <div class="card-body card-body-mobile scroll-container">
                <table class="table table-sm table-borderless">
                  <thead>
                    <tr>
                      <th scope="col">Dates</th>
                      <th scope="col">Type</th>
                      <th scope="col">Download</th>
                      <th scope="col">Upload</th>
                      <th scope="col">Review</th>
                      <th scope="col">&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in getActionRequiredList()" v-bind:key="item.EducAssignmentId" >
                      <td>{{item.Date}}</td>
                      <td>{{item.SessionType}}</td>
                      <td>
                        <div class="icon-spinner text-center mt-n2" v-if="item.isDownloadFileInProgress"></div>
                        <div class="icon-box" v-else>
                          <a data-toggle="tooltip" data-placement="top" title="Download your contract file">
                            <font-awesome-icon icon="file-download" alt="Download file" style="font-size: 32px; color: #003366;"
                              @click="showFileDownload(item)" />
                          </a>
                        </div>
                      </td>
                      <td>
                        <div class="icon-box">
                          <a data-toggle="tooltip" data-placement="top" title="Upload your signed contract file">
                            <font-awesome-icon icon="file-upload" alt="Upload file" style="font-size: 32px; color: #f5a742;" 
                              @click="showFileUpload(item.EducAssignmentId)" />
                          </a>
                        </div>
                      </td>
                      <td>
                        <div class="icon-spinner text-center mt-n2" v-if="item.isUploadedFilesInProgress"></div>
                        <div class="icon-box" v-else>
                          <a data-toggle="tooltip" data-placement="top" title="Review the uploaded files">
                            <font-awesome-icon :icon="['far','file']" alt="List of uploaded files" style="font-size: 32px;"
                              @click="showFileUploadedList(item)" />
                          </a>
                        </div>
                      </td>
                      <td>
                        <div class="icon-spinner text-center mt-n2" v-if="item.isSubmitInProgress"></div>
                        <div class="float-right" v-else>
                          <button class="btn btn-block btn-primary" data-toggle="tooltip" data-placement="top" title="Submit for review"
                            @click="showSubmitForReview(item)">
                            Submit
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
              <font-awesome-icon v-else class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
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
                    <tr v-for="item in getPendingReviewedList()" :key="item.EducAssignmentId" >
                      <td>{{item.Date}}</td>
                      <td>{{item.SessionType}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="card mt-2">
            <div class="card-header" id="finalizedSectionHeader" @click="toggleFinalized()">
              <font-awesome-icon v-if="!finalizedDisplayed" class="float-right" icon="angle-right" alt="Show contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <font-awesome-icon v-else class="float-right" icon="angle-down" alt="Hide contents" style="margin-top: 12px; font-size: 32px; color: #003366;"  />
              <h5 class="pt-1">
                  Finalized
              </h5>
              <p class="pt-1 mb-0">Your finalized contract(s) for upcoming session(s).</p>
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
                    <tr v-for="item in getFinalizedList()" :key="item.EducAssignmentId">
                      <td>{{item.Date}}</td>
                      <td>{{item.SessionType}}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <modal name="help_form2" height="auto" :scrollable="false" :clickToClose="false">
         <help formName="help_form2"/>
      </modal>
      <modal name="file_upload_form" width ="370px" height="auto" :scrollable="false" :clickToClose="false">
         <file-uploader :assignmentID="selectedAssignmentID"/>
      </modal>
       <modal name="file_uploaded_list"  height="auto" :scrollable="false" :clickToClose="false">
         <file-uploaded-list :files="uploaded_files"/>
      </modal>
      <modal name="file_download_form" height="auto" :scrollable="false" :clickToClose="false">
         <file-downloader :contract="contract"/>
      </modal>
      <modal name="submit_for_review_form" height="auto" :scrollable="false" :clickToClose="false">
         <submit-for-review :files="uploaded_files" :assignmentID="selectedAssignmentID"/>
      </modal>
      <modal name="warning_form1" height="auto" :scrollable="false" :clickToClose="false">
        <div class="card">
          <div class="card-header">
              <button class="btn btn-danger btn-sm float-right" v-on:click="closeWarning">X</button>
              <font-awesome-icon class="float-left" icon="exclamation-triangle" alt="Hide warning" style="margin-top: 4px; font-size: 24px; color: #f5a742;"  />
              <h4 class="ml-2 pl-4">Warning</h4>
          </div>
          <div class="card-body">
              <h5>There is no uploaded file to submit.</h5>
          </div>
          <div class="card-footer">
          </div>
        </div> 
      </modal>
    </div>
</template>

<script>
import {mapGetters} from 'vuex'
import FileUploader from './FileUploader.vue';
import FileUploadedList from './FileUploadedList.vue'
import FileDownloader from './FileDownloader.vue'
import SubmitForReview from './SubmitForReview.vue'
import Help from './Help.vue'

export default {
    name: "Contracts",
    components: {
        FileUploader,
        FileUploadedList,
        FileDownloader,
        SubmitForReview,
        Help
    },
    computed: {
        ...mapGetters([
            'getUser',
            'getSessions',
            'getAssignments',
        ]),
    },
    watch: {
      getAssignments: {
        deep: true,
        handler() {
          this.assignments = this.getAssignments.filter(item => !!item);
          console.log(`${this.assignments.length} assignment(s) re-loaded.`);
        }
      }
    },
    data() {
      return {
        assignments: [],
        uploaded_files: [], // for uploaded files
        contract: {}, // for file download
        selectedAssignmentID: null, // for file upload & submit for review
        actionRequiredDisplayed: true,
        pendingReviewDisplayed: false,
        finalizedDisplayed: false,
      }
    },
    mounted() {
      console.log('My Contracts Mounted')

      this.assignments = this.getAssignments.filter(item => !!item);
      console.log(`${this.assignments.length} assignment(s) loaded.`);
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
        this.$modal.show('help_form2');
      },
      showFileUpload(assignmentID) {
        this.selectedAssignmentID = assignmentID;
        this.$modal.show('file_upload_form');
      },
      closeWarning() {
        this.$modal.hide('warning_form1');
      },
      async showSubmitForReview(item) {
        item.isSubmitInProgress = true;
        this.uploaded_files = !!item.EducAssignmentId? await this.loadUploadedFiles(item.EducAssignmentId) : [];
        console.log('uploaded files: ' + this.uploaded_files.length);
        item.isSubmitInProgress = false;

        if (this.uploaded_files.length === 0) {
          this.$modal.show('warning_form1');
        } else {
          this.selectedAssignmentID = item.EducAssignmentId;
          this.$modal.show('submit_for_review_form');
        }
      },
      async showFileUploadedList(item) {
        item.isUploadedFilesInProgress = true;
        this.uploaded_files = !!item.EducAssignmentId? await this.loadUploadedFiles(item.EducAssignmentId) : [];
        console.log('uploaded files: ' + this.uploaded_files.length);
        item.isUploadedFilesInProgress = false;
        this.$modal.show('file_uploaded_list');
      },
      async showFileDownload(item) {
        item.isDownloadFileInProgress = true;
        const res = !!item.EducAssignmentId? await this.getContract(item.EducAssignmentId) : null;
        console.log('get contract: ' + JSON.stringify(res));
        item.isDownloadFileInProgress = false;
        this.contract = res && res.length > 0? res[0] : {};
        this.$modal.show('file_download_form');
      },
      getActionRequiredList() {
        if (this.assignments && this.assignments.length > 0) {
         return this.assignments.filter(c => c.EducContractStage  === 'Contract Sent')
        }
        return [];
      },
      getPendingReviewedList() {
        if (this.assignments && this.assignments.length > 0) {
          return this.assignments.filter(c => c.EducContractStage  === 'Contract Submitted')
        }
        return [];
      },
      getFinalizedList() {
        if (this.assignments && this.assignments.length > 0) {
          return this.assignments.filter(c => c.EducContractStage  === 'Contract Signed')
        }
        return [];
      },
      loadUploadedFiles(assignmentID) {
        return axios.get(`/api/${assignmentID}/listuploadedfiles`)
          .then( response => {
              console.log('list uploaded files api returned:  ', response.data  );
              return response.data.ListUploadedFiles;
          })
          .catch( error => {
              console.log('Fail to load uploaded files!', error);
              //this.isUploadedFilesInProgress = false;
              return [];
          });
      },
      getContract(assignmentID) {
        return axios.get(`/api/${assignmentID}/contractfile`)
          .then( response => {
              console.log('list uploaded files api returned:  ', response.data  );
              return response.data.ContractFile;
          })
          .catch( error => {
              console.log('Fail to load uploaded files!', error);
              //this.isDownloadFileInProgress = false;
              return null;
          });
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

.icon-box {
  padding-top: 2px;
  padding-right: 4px;
  text-align: center;
}

.icon-box:hover {
  cursor: pointer;
}


</style>
