<template>
  <div id="app">
    <resource-editor></resource-editor>
  </div>
</template>

<script>
import resourceEditor from './components/resourceEditor';

export default {
  name: 'App',
  components: {
    'resource-editor': resourceEditor
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
