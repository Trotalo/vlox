<template>
  <div id="app">
    <img alt="Vue logo" src="assets/logo.png">
    <HelloWorld msg="Welcome to Your fucking@! Vue.js App"/>
    {{test}}
  </div>
</template>

<script>
import HelloWorld from './components/HelloWorld.vue'

export default {
  name: 'App',
  data: function() {
    return {
      test: 'juanito'
    }
  },
  components: {
    HelloWorld
  }
}
</script>

<style lang="scss">
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
