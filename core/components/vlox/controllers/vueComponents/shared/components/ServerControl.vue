<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <div class="previewButtons">
    <b-button variant="success" @click="runServer()" class="updatePrev">RUN</b-button>
    <b-button variant="outline-primary" @click="saveChanges()" class="updatePrev ml-4 mr-4">PREVIEW asdf</b-button>
    <b-button variant="danger" @click="stopServer()" class="updatePrev">STOP</b-button>
  </div>
</template>

<script>
import axios from "axios";

const axiosConfig = {
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': "*",
  }
}

export default {
  name: "ServerControl",
  props: ['resourceId', 'saveMethod', 'saveObject'],
  methods: {
    runServer() {
      debugger;
      axios.put(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=Ide/'
          + this.resourceId,
          {'oper': 'RUN'},
          axiosConfig)
          .then(response => {
            console.log(response);
          })
          .catch(error => {
            console.log(error);
          });
    },
    stopServer() {

    },
    saveChanges() {//
      debugger;
      if (this.saveMethod && this.saveMethod instanceof Function) {
        this.saveMethod(this.resourceId)
      } else {
        axios.put(window.location.protocol + "//" + window.location.host +
            this.$restRoute + '/rest/index.php?_rest=Ide/'
            + this.resourceId,
            {'oper': 'UPDATE'},
            axiosConfig)
            .then(response => {
              console.log(response);
            })
            .catch(error => {
              console.log(error);
            });
      }

    },
  }
}
</script>

<style>

</style>