<template>
  <div class="card">
    <div class="card-header">
        <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
        <h2>Upload your signed contract</h2>
    </div>
    <div class="card-body card-body-custom">
      <div v-if="errorMessages.length" class="alert alert-danger">
          <ul >
            <li v-for="(message, index) in errorMessages" :key="index">{{message }}</li>
          </ul>
        </div>
      
      <div class="file-upload-div">
        <div class="upload">
          <ul v-if="files.length">
            <li v-for="file in files" :key="file.id">
              <span>{{file.name}}</span> -
              <span>{{formatBytes(file.size)}}</span> -
              <span v-if="file.error">{{file.error}}</span>
              <span v-else-if="file.success" class="text-primary">success</span>
              <span v-else-if="file.active" class="text-danger">active</span>
              <span v-else></span>
            </li>
          </ul>
          <ul v-else>
            <table class="table-spacing">
              <tr>
                <td colspan="7">
                  <div class="text-center p-5 drop-area">
                    <h4>Drag and drop here<br/>or</h4>
                    <label for="file" class="btn btn-primary">Browse</label>
                  </div>
                </td>
                </tr>
              <tr>
                <td>
                  <div class="mt-n4 pt-2 pl-2">
                    Accepted file formats: <br/>
                    <ul>
                      <li>pdf</li>
                      <li>jpeg</li>
                      <li>png</li>
                    </ul>
                    <br/>
                    Max file size: 3MB
                  </div>
                </td>
              </tr>
          </table>
          </ul>

          <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
            <h3>Drop files to upload</h3>
          </div>

          <div  @change="checkFileSize" class="file-upload-btn-group">
            <file-upload
              v-show="false"
              class="btn btn-primary"
              :post-action="getUploadPostUrl"
              :multiple="true"
              :drop="true"
              :drop-directory="false"
              accept="application/pdf,image/png,image/jpeg"
              :size="1024*1024*3"
              v-model="files"
              ref="upload">
              <i class="fa fa-plus"></i>
              Select files
            </file-upload>
            <div class="btn-group-box" v-if="$refs.upload && $refs.upload.uploaded && uploadCompleted()">
              <span class="text-success">All file(s) have been uploaded.</span>
              <button type="button" class="btn btn-outline-primary ml-4 mr-3" v-on:click="closeModal">
                Close
              </button>
            </div>
            <div class="btn-group-box" v-else>
              <button type="button" class="btn btn-success mr-3" v-if="files.length&&(!$refs.upload || !$refs.upload.active)" @click.prevent="$refs.upload.active = true">
                <i class="fa fa-arrow-up" aria-hidden="true"></i>
                Upload
              </button>
              <button type="button" class="btn btn-warning mr-3"  v-else-if="files.length&&$refs.upload.active" >
                <i class="fa fa-stop" aria-hidden="true"></i>
                Uploading...
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer">
    </div>
  </div>
</template>

<script>
import FileUpload from 'vue-upload-component'
import { formatBytes } from '../util'
export default {
  name: "FileUploader",
  components: {
    FileUpload,
  },
  props: {
    assignmentID: {
      type: String,
      required: true
    },
  },
  mounted() {
    this.selectedAssignmentId = this.assignmentID;
  },
  watch: {
    assignmentID(newValue) {
        this.selectedAssignmentId = newValue;
    },
  },
  data() {
    return {
      files: [],
      selectedAssignmentId: null,
      errorMessages: [],
    }
  },
  computed: {
    getUploadPostUrl() {
      return 'api/' + this.selectedAssignmentId + '/fileupload';
    }
  },
  methods: {
    formatBytes,
    closeModal() {
        this.$modal.hide('file_upload_form');
    },
    checkFileSize() {
      this.errorMessages = [];
      this.files.forEach(file =>{
      if (file.size> 1024*1024*3) {
        this.errorMessages.push('File exceeds 3MB')
      }
      if (this.errorMessages.length>0){
        this.files =[];
      } else {
        this.files = files;
      }
     });
     },
    uploadCompleted() {
      if (this.files.length === 0) {
        return false;
      }
      return this.files.filter(f => !f.success).length === 0;
    }
  }
}
</script>

<style scoped>

.file-upload-div label.btn {
  margin-bottom: 0;
  margin-right: 1rem;
}
.file-upload-div .drop-active {
  top: 0;
  bottom: 0;
  right: 0;
  left: 0;
  position: fixed;
  z-index: 9999;
  opacity: .6;
  text-align: center;
  background: #000;
}
.file-upload-div .drop-active h3 {
  margin: -.5em 0 0;
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
  transform: translateY(-50%);
  font-size: 40px;
  color: #fff;
  padding: 0;
}

.upload {
  padding-top: 10px;
}

.drop-area {
  border: 1px blue dotted;
}

.file-upload-btn-group {
  display: flex;
  justify-content: flex-end;
}
.table-spacing {
  border-spacing: 0 20px;
  border-collapse: separate !important;
}
.card-body-custom {
  flex: 1 1 auto;
   padding: 0rem;
}
</style>