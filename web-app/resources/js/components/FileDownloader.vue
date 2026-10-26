<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Download contract</h2>
        </div>
        <div class="card-body">
            <div class="file-download-div">
                <div class="mx-2 my-2">
                    <span class="pl-2"><a href="#"
                      data-toggle="tooltip" data-placement="bottom" title="Click to download the contract file"
                      @click.prevent="downloadFile">{{current_contract.FileName}}</a></span>
                </div>
            </div>
        </div>
        <div class="card-footer">
        </div>
    </div>
</template>

<script>
export default {
  name: "FileDownloader",
  props: {
    contract: {
      type: Object,
      required: true
    },
  },
  mounted() {
    this.current_contract = this.contract;
  },
  watch: {
    contract(newValue) {
      this.current_contract = newValue;
    },
  },
  data() {
    return {
      current_contract: {}
    }
  },
  methods: {
    closeModal() {
      this.$modal.hide('file_download_form');
    },
    // download through axios so the request carries the login token, then save the file
    downloadFile() {
      return axios.get(`/api/${this.current_contract.AnnotationId}/filedownload`, { responseType: 'blob' })
        .then(response => {
          const url = window.URL.createObjectURL(response.data);
          const link = document.createElement('a');
          link.href = url;
          link.download = this.current_contract.FileName;
          document.body.appendChild(link);
          link.click();
          link.remove();
          setTimeout(() => window.URL.revokeObjectURL(url), 10000);
        })
        .catch(error => {
          console.log('Fail to download file!', error);
        });
    },
  }
}
</script>

<style scoped>
.file-download-div a {
   color: blue;
}

.file-download-div a:hover {
   text-decoration: underline;
   font-weight: bold;
   color: blue;
}
</style>