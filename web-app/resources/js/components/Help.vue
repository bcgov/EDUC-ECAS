<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeHelp">X</button>
            <h2>Help</h2>
        </div>
        <div class="card-body">
            <p>For assistance, please email questions or concerns to assessments@gov.bc.ca</p>
        </div>
        <div class="card-footer">
        </div>
    </div> 
</template>

<script>
export default {
  name: "Help",
  props: {
      formName: '',
  },
  methods: {
    closeHelp() {
        this.$modal.hide(this.formName);
    }
  }
}
</script>
