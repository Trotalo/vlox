<template>
  <div id="app">
    <loading></loading>
    <editor-home></editor-home>
  </div>
</template>

<script>

import editorHome from './components/editorHome';
import loading from '@shared/components/loading'

export default {
  name: 'App',
  components: {
    editorHome,
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
  background-color: #f2f2f2;

  .vloxLogo {
    display: block;
    max-width: 200px;
    margin: 0 auto;
  }
  .container {
    max-width: 1200px;
  }
  .text-right {
    text-align: right;
  }
  .btn {
    margin-bottom: .5rem;
    text-transform: initial;
    text-transform: initial;
    font-weight: bold;
    font-size: .8rem;
    padding: 8px;
    text-transform: uppercase;
  }
  .btn-outline-primary, .btn-check:focus+.btn, .btn:focus  {
    color: #525252;
    border-color: #525252;
  }
  .btn-success:focus {
    color: white !important;
  }
  .btn-check:focus+.btn, .btn:focus, .btn-check:active+.btn-outline-primary:focus, .btn-check:checked+.btn-outline-primary:focus, .btn-outline-primary.active:focus, .btn-outline-primary.dropdown-toggle.show:focus, .btn-outline-primary:active:focus {
    box-shadow: none !important;
  }
  .btn-outline-primary:hover {
    color: white;
    background-color: #f8a000;
    border-color: #b07100;
  }
  .nav-link, .nav-link:hover {
    color: #f8a000;
    font-weight: 400;
  }
  .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
    color: #495057;
    background-color: transparent;
    border-color: #dee2e6 #dee2e6 #f2f2f2;
  }
  .nav-tabs .nav-link {
    color: #495057;
  }
  .nav-link, .nav-link:hover {
    color: #ffffff;
    background: #f8a000;
  }
  .smallForm {
    max-width: 500px;
    margin: 0 auto;
  }
  .small, small {
    font-size: .8rem !important;
  }
  h5 {
    word-break: break-all;
    padding: 0 .5rem;
  }
  .lightGrey {
    color: #9b9b9b;
    font-weight: 400;
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity .5s;
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
    opacity: 0;
  }
}

.dg-content-cont--floating{
  top: 10vh !important;
  transform: translateY(0%) !important;
}


</style>
