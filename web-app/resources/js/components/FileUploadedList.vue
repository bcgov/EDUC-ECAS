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
                    <tr>
                        <td>Untitled.pdf</td>
                        <td>205 KB</td>
                        <td>&nbsp;</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Untitled2.pdf</td>
                        <td>312 KB</td>
                        <td>&nbsp;</td>
                        <td>
                            <button class="btn btn-sm btn-outline-danger">
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
  name: "FileUploadedList",
  data() {
    return {
      files: [],
    }
  },
  methods: {
    closeModal() {
        this.$modal.hide('file_uploaded_list');
    },
  }
}
</script>