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

<!--    <br>-->
<!--    <b-button @click="getNpmStatus()" class="updatePrev mr-2">NPM Status</b-button>-->
    <b-button variant="outline-primary" v-b-modal.project_config type="button" class="updatePrev ml-2">Configuration</b-button>
    <p>{{isRunning ? 'Is Running' : 'Stopped'}}</p>
    <b-button v-if="!isRunning" :disabled="isRunning" variant="success" @click="runServer()" class="mr-3">RUN</b-button>
    <b-button v-else variant="danger" @click="stopServer()" class="mr-3">STOP</b-button>

    <b-button v-if="vloxType === 0" @click="saveChanges()" class="mr-3" variant="outline-primary">SAVE</b-button>

    <b-button @click="reloadServerFiles">Refresh</b-button>

    <br>
<!--    <b-button @click="reloadServerFiles" class="updatePrev ml-3">RELOAD</b-button>-->
    <base-configuration :resource-id="resourceId"></base-configuration>

    <b-modal id="npm-status-modal" size="xl" title="NPM Status" ok-only>
      <p class="log-view">{{npmStatus}}</p>
    </b-modal>
  </div>
</template>

<script>
import Services from '@shared/services';
import baseConfiguration from './baseConfiguration'
import { mapActions } from 'vuex';

export default {
  name: "ServerControl",
  components: {'base-configuration': baseConfiguration},
  props: {
      resourceId: {
        type: Number,
        required: true
      },
      blockData : {
        type: Object,
      },
      vloxType: {
        type: Number,
        required: true
      }
    },
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
      const startResponse = await Services.startServer(resourceId, this.vloxType);
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
    async stopServer() {
      this.showLoading();
      await Services.stopServer();
      this.isRunning = false;
      this.refreshView();
      this.hideLoading();
    },
    async saveChanges() {//
      if (this.vloxType === 0) {
        await Services.saveBlockData(this.blockData);
        //after saved generate the image!
        //debugger;
      } else {
        let finalObject = JSON.parse(JSON.stringify(this.blockData));
        finalObject['items'] = this.vloxContent['properties']['items'];
        delete finalObject['properties'];
        await Services.saveResData(finalObject);
      }
      //const response = await Services.updateIde(this.resourceId, this.vloxType);
      return true;
    },
    async reloadServerFiles() {
      //const response = await Services.buildResource(this.resourceId);
      await Services.updateIde(this.resourceId, this.vloxType);
      this.refreshView();
      //return response;
    },
    async loadRunningStatus() {
      this.intervalId = setInterval(async() => {
        try {
          const response = await Services.getNpmStatus(this.resourceId);
          this.isRunning = response.object;
        } catch (e) {
          await this.$dialog.alert('Error contacting server!');
        }
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
    if (this.isRunning) {
      await Services.stopServer();
    }
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