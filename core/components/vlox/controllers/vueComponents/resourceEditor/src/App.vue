<template>
  <div id="app">
    <loading></loading>
    <resource-editor></resource-editor>
  </div>
</template>

<script>
import resourceEditor from './components/resourceEditor';
import loading from '@shared/components/loading'

export default {
  name: 'App',
  components: {
    resourceEditor,
    loading
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

.dg-content-cont--floating{
  top: 10vh !important;
  transform: translateY(0%) !important;
}
</style>
