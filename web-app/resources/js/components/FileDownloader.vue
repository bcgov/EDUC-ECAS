<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeModal">X</button>
            <h2>Download file</h2>
        </div>
        <div class="card-body">
            <div class="file-download-div">
                <div class="download mx-2 my-2">
                    <span class="pl-2"><a href="#">Jenni McConnell - E21-12345</a></span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: "FileDownloader",
  data() {
    return {
      files: [],
    }
  },
  methods: {
    closeModal() {
        this.$modal.hide('file_download_form');
    },
  }
}
</script>

<style scoped>
.download {
  border: 1px #ff0000 dotted;
}
</style>