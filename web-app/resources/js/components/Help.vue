<template>
    <div class="card">
        <div class="card-header">
            <button class="btn btn-primary btn-sm float-right" v-on:click="closeHelp">X</button>
            <h2>Help</h2>
        </div>
        <div class="card-body">
            <p>If you have questions or concerns, or if you wish to request a direct deposit payment application form, please email <a href="mailto:assessments@gov.bc.ca">assessments@gov.bc.ca</a> for assistance. </p>
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
