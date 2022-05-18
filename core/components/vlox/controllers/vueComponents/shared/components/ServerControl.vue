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
    <b-button :disabled="!isRunning" @click="saveChanges()" class="updatePrev ml-4 mr-4">SAVE</b-button>
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
import { mapActions } from 'vuex';


const axiosConfig = {
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': "*",
  }
}

export default {
  name: "ServerControl",
  components: {'base-configuration': baseConfiguration},
  props: ['resourceId', 'blockData'],
  data() {
    return {
      npmStatus: '',
      isRunning: false,
      intervalId: 0,
    }
  },
  methods: {
    ...mapActions([
      'showLoading', 'hideLoading']),
    async getNpmStatus(){
      let response = await Services.getNpmLog();
      this.npmStatus = response.object;
      this.$bvModal.show('npm-status-modal');
    },
    async startServer(resourceId){
      this.showLoading();
      const startResponse = await Services.startServer(resourceId);
      console.log(startResponse);
      setTimeout(()=> {
        this.hideLoading();
        this.refreshView();
        this.$dialog.alert("allow a couple seconds for node server to start, " +
            "if it's taking too long, or the state doesn't change to running, check the NPM STATUS")
            .then(()=>{
              console.log('closed');
            })

      }, 3000);
      return;
    },
    async runServer() {
      const isNpmInstalled = await Services.isNpmInstalled();
      if (!isNpmInstalled.object) {
        //if npm isn't installed, show confirm and proceed with installation
        this.$dialog.confirm("NPM isn't installed on the server, " +
            "this installation can take some time, want to continue?")
          .then(async () => {
            this.showLoading();
            const installationResponse = await Services.installNpm();
            await this.startServer(this.resourceId);
            console.log(installationResponse);
          })
          .catch((error) => {
            console.error(error);
          })
      } else {
        await this.startServer(this.resourceId);
      }
    },
    stopServer() {
      this.showLoading();
      axios.put(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=Ide/'
          + this.resourceId,
          {'oper': 'STOP'},
          axiosConfig)
          .then(response => {
            console.log(response);
            this.refreshView();
            this.hideLoading();
          })
          .catch(error => {
            console.log(error);
          });
    },
    async saveChanges() {//
      let response = await Services.saveBlockData(this.blockData);
      console.log(response);
      response = await Services.updateIde(this.resourceId);
    },
    async loadRunningStatus() {
      this.intervalId = setInterval(async() => {
        const response = await Services.getNpmStatus(this.resourceId);
        this.isRunning = response.object;
      }, 3000);

    },
    refreshView() {
      document.getElementById('componentPreview').src
          = document.getElementById('componentPreview').src;
    }
  },
  async mounted() {
    const response = await Services.getNpmStatus(this.resourceId);
    this.isRunning = response.object;
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