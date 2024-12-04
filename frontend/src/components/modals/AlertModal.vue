<template>
  <div class="alert-modal">
    <div class="modal-content">
      <h2>{{ title }}</h2>
      <p>{{ message }}</p>
      <button @click="$emit('close')" class="close-btn">Close</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AlertModal',
  props: {
    title: {
      type: String,
      default: 'Alert'
    },
    message: {
      type: String,
      required: true
    }
  }
}
</script>

<style scoped>
.alert-modal {
  /* Add your styles */
}
</style> 