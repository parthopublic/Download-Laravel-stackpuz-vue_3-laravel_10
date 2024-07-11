<template>
  <div class="center-container home">
  <div class="row justify-content-center">
    <h3>Not found!</h3>
  </div>
</div>
</template>
<script>
  export default {
    name: 'NotFound',
    mounted() {
      if (location.hash) {
        this.$router.push(location.hash.substr(1))
      }
    }
  }
</script>