<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <div v-if="isLoading" class="custom-loader">
    <b-spinner variant="danger"></b-spinner>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';


export default {
  name: "loading",
  computed: {
    ...mapGetters(['showLoading']),
    isLoading() {
      return this.showLoading;
    }

  },
}
</script>

<style scoped>
.custom-loader{
  width: 100%;
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 5000;
  background-color: #00000070;
  padding-top: 10vh;
  /*position: absolute;
  background-color: #00000070;
  height: 100%;
  width: 100%;
  z-index: 9999;*/
  /*padding-top: 10vh;
  margin-top: -60px;*/
}
</style>