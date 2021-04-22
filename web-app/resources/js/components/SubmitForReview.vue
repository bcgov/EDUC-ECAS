<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Confirmation to submit</h2>
        </div>
        <div class="card-body">
            <div class="text-primary mx-2 mb-2">
                <p>Once you've submitted your contract, you will no longer be able to view it.  Please save a copy for your records.</p>
            </div>
            <hr/>
            <table class="table table-sm table-borderless my-2">
                <thead>
                    <tr>
                        <th scope="col">File name</th>
                        <th scope="col">Size</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in uploaded_files" :key="item.AnnotationId">
                        <td>{{item.FileName}}</td>
                        <td>{{item.FileSizeVal}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            <div class="btn-group-box">
                <div class="col">
                    <div class="icon-spinner text-center mt-n2" v-if="isSubmitInProgress"></div>
                    <button class="btn btn-danger btn-block" v-else v-on:click.prevent="closeModal()">Cancel</button>
                </div>
                <div class="col">
                    <div class="icon-spinner text-center mt-n2" v-if="isSubmitInProgress"></div>
                    <button class="btn btn-primary btn-block" v-else v-on:click.prevent="performSubmit()" dusk="save">
                        Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "SubmitForReview",
  props: {
    files: {
        type: Array,
        required: true
    },
    assignmentID: {
        type: String,
        required: true
    },
  },
  mounted() {
      if (this.files && this.files.length > 0) {
          this.uploaded_files = this.files;
      }
      this.selectedAssignmentId = this.assignmentID;
  },
  watch: {
      files(newValue) {
          this.uploaded_files = newValue;
      },
      assignmentID(newValue) {
          this.selectedAssignmentId = newValue;
      },
  },
  data() {
    return {
      uploaded_files: [],
      selectedAssignmentId: null,
      isSubmitInProgress: false,
    }
  },
  computed: {
      getSubmitUrl() {
        return 'api/' + this.selectedAssignmentId + '/filesubmit';
      }
  },
  methods: {
    closeModal() {
        this.$modal.hide('submit_for_review_form');
    },
    submitForReview() {
        return axios.patch(this.getSubmitUrl)
            .then( response => {
                console.log('file submit api returned:  ', response.data  );
                return response.data;
            })
            .catch( error => {
                console.log('Fail to submit file for review!', error);
                this.isSubmitInProgress = false;
                return null;
            });
    },
    async performSubmit() {
        this.isSubmitInProgress = true;
        const res = await this.submitForReview(this.selectedAssignmentId);
        if (res && res.status === 200) {
            Event.fire('user-assignments-updated', res );
        } else {
            // show error message to user
        }
        this.isSubmitInProgress = false;
    },
  },
}
</script>

<style scoped>
.btn-group-box {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>