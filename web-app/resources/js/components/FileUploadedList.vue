<template>
    <div class="card">
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
                        <td>{{item.FileSizeVal}}</td>
                        <td>&nbsp;</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger" @click="deleteFile(item.AnnotationId)">
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
</template>

<script>
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
  data() {
    return {
      uploaded_files: [],
    }
  },
  methods: {
    closeModal() {
        this.$modal.hide('file_uploaded_list');
    },
    deleteFile(annotationID) {
        axios.delete(`/api/${annotationID}/filedelete`)
        .then( response => {
            //
            this.uploaded_files = this.uploaded_files.filter(f => f.annotationID !== annotationID);
        })
        .catch( error => {
            console.log('Fail to delete file!', error);
        })
    }
  }
}
</script>