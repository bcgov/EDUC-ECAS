<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Download contract</h2>
        </div>
        <div class="card-body">
            <div class="file-download-div">
                <div class="mx-2 my-2">
                    <span class="pl-2"><a data-toggle="tooltip" data-placement="bottom" title="Click to download the contract file"
                     @click="downloadFile(current_contract.AnnotationId)">{{current_contract.FileName}}</a></span>
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
    downloadFile(annotationID) {
      console.log('file download :  ', `/api/${annotationID}/filedownload`);
      axios.get(`/api/${annotationID}/filedownload`)
        .then( response => {
            console.log('file download api returned:  ', response.data  );
        })
        .catch( error => {
            console.log('Fail!', error);
        });
    }
  }
}
</script>

<style scoped>
.file-download-div a:hover {
   text-decoration: underline;
   font-weight: bold;
}
</style>