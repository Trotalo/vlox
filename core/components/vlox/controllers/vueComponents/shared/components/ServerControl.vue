<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <div class="previewButtons mt-4">
    <b-button :disabled="isRunning" variant="success" @click="runServer()" class="updatePrev">RUN</b-button>
    <b-button :disabled="!isRunning" @click="saveChanges()" class="updatePrev ml-4 mr-4">PREVIEW</b-button>
    <b-button :disabled="!isRunning" variant="danger" @click="stopServer()" class="updatePrev">STOP</b-button>
    <br>
    <p>is running: {{isRunning}}</p>
    <b-button @click="refreshView()" class="updatePrev mr-2">Refresh</b-button>
    <br>
    <b-button @click="getNpmStatus()" class="updatePrev mr-2">NPM Status</b-button>
    <b-button variant="outline-primary" v-b-modal.project_config type="button" class="updatePrev ml-2">Configuration</b-button>
    <base-configuration :resource-id="resourceId"></base-configuration>
    <b-modal id="npm-status-modal" size="xl" title="NPM Status" ok-only>
      <p class="log-view">{{npmStatus}}</p>
    </b-modal>
  </div>
</template>

<script>
import axios from "axios";
import Services from '@shared/services';
import baseConfiguration from './baseConfiguration'
//import vueAceEditor from "./vueAceEditor";


const axiosConfig = {
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': "*",
  }
}

export default {
  name: "ServerControl",
  components: {'base-configuration': baseConfiguration},
  props: ['resourceId'],
  data() {
    return {
      npmStatus: '',
      isRunning: false,
      intervalId: 0,
    }
  },
  methods: {
    async getNpmStatus(){
      let response = await Services.getNpmLog();
      this.npmStatus = response.object;
      this.$bvModal.show('npm-status-modal');
    },
    runServer() {
      axios.put(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=Ide/'
          + this.resourceId,
          {'oper': 'RUN'},
          axiosConfig)
          .then(response => {
            this.loadRunningStatus();
            setTimeout(()=> {
              document.getElementById('componentPreview').src =
                  document.getElementById('componentPreview').src;
            }, 3000)
            console.log(response);
          })
          .catch(error => {
            console.log(error);
          });
    },
    stopServer() {
      axios.put(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=Ide/'
          + this.resourceId,
          {'oper': 'STOP'},
          axiosConfig)
          .then(response => {
            console.log(response);
            document.getElementById('componentPreview').src =
                document.getElementById('componentPreview').src;
          })
          .catch(error => {
            console.log(error);
          });
    },
    saveChanges() {//
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
    async loadRunningStatus() {
      this.intervalId = setInterval(async() => {
        const response = await Services.getNpmStatus(this.resourceId);
        this.isRunning = response.object;
        if (!this.isRunning) {
          clearInterval(this.intervalId);
        }
      }, 2000);

    }
  },
  mounted() {
    this.loadRunningStatus();
  },
  beforeDestroy() {
    clearInterval(this.intervalId);
  }/*,
  async beforeUpdate() {
    await this.loadRunningStatus();
  }*/
}
</script>

<style>
 .log-view{
   overflow-x: scroll;
   overflow-y: scroll;
   max-height: 75vh;
   white-space: pre;
 }
</style>