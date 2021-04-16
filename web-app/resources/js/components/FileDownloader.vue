<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Download contract</h2>
        </div>
        <div class="card-body">
            <div class="file-download-div">
                <div class="mx-2 my-2">
                    <span class="pl-2"><a href="#">Jenni McConnell - E21-12345</a></span>
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
    contract: null
  },
  mounted() {
    this.current_contract = this.contract;
  },
  data() {
    return {
      current_contract: null
    }
  },
  methods: {
    closeModal() {
      this.$modal.hide('file_download_form');
    },
    downloadFile(annotationID) {
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
