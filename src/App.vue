<template>
  <div id="app">
    <royal-nav/>
    <div class="content">
      <router-view/>
    </div>
  </div>
</template>

<script>
import royalNav from './components/RoyalNav.vue'

export default {
  name: 'Notes',
  components: {
    royalNav
  }
}
</script>

<style lang="scss">
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;

  .content {
    margin-top: 75px;
  }
}
</style>
