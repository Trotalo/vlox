<template>
  <div id="app">
    <editor-home></editor-home>
  </div>
</template>

<script>

import editorHome from './components/editorHome';

export default {
  name: 'App',
  components: {
    editorHome,
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
