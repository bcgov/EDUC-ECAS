<template>
    <div class="card" v-if="!confirmationMode">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Uploaded files</h2>
        </div>
        <div class="card-body">
            <table class="table table-sm table-borderless my-2">
                <thead>
                    <tr>
                        <th scope="col">File name</th>
                        <th scope="col">Size</th>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in uploaded_files" :key="item.AnnotationId">
                        <td>{{item.FileName}}</td>
                        <td>{{formatBytes(item.FileSize)}}</td>
                        <td>&nbsp;</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#confirmDeleteModal" @click="showConfirmationModal(item.AnnotationId)">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer">
        </div>
    </div>
    <div class="card" v-else>
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="hideConfirmationModal">X</button>
            <h2>Confirmation to delete file</h2>
        </div>
        <div class="card-body">
            <p>Do you want to procceed to delete <span class="text-primary">{{getSelectedFileName()}}</span>?</p>
        </div>
        <div class="card-footer">
            <div class="btn-group-box">
                <div class="col">
                    <button type="button" class="btn btn-danger btn-block" @click="hideConfirmationModal">Cancel</button>
                </div>
                <div class="col">
                    <button v-if="!this.isInProgress" type="button" class="btn btn-primary btn-block" @click="deleteFile()">Proceed</button>
                    <button v-else type="button" class="btn btn-warning btn-block">Deleting...</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { formatBytes } from '../util'
export default {
  name: "FileUploadedList",
  props: {
      files: {
          type: Array,
          required: true
      },
  },
  mounted() {
      if (this.files && this.files.length > 0) {
          this.uploaded_files = this.files;
      }
  },
  watch: {
      files(newValue) {
          this.uploaded_files = newValue;
      },
  },
  data() {
    return {
      uploaded_files: [],
      confirmationMode: false,
      selectedAnnotationId: null,
      isInProgress: false,
    }
  },
  methods: {
    formatBytes,
    closeModal() {
        this.$modal.hide('file_uploaded_list');
    },
    showConfirmationModal(annotationID) {
        this.selectedAnnotationId = annotationID;
        this.confirmationMode = true;
    },
    hideConfirmationModal() {   
        this.selectedAnnotationId = null;
        this.confirmationMode = false;
    },
    getSelectedFileName() {
        if (this.selectedAnnotationId) {
            return this.uploaded_files.find(f => f.AnnotationId === this.selectedAnnotationId).FileName;
        }
        return '';
    },
    deleteFile() {
        this.isInProgress = true;
        axios.delete(`/api/${this.selectedAnnotationId}/filedelete`)
        .then(() => {
            //
            this.uploaded_files = this.uploaded_files.filter(f => f.AnnotationId !== this.selectedAnnotationId);
            this.hideConfirmationModal();
            this.isInProgress = false;
        })
        .catch( error => {
            this.isInProgress = false;
            console.log('Fail to delete file!', error);
        })
    }
  }
}
</script>

<style scoped>
.btn-group-box {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
}
</style>