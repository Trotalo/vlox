<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <b-modal id="add-content" title="Add Block" size="xl" scrollable :hide-footer="true">
    <div class="content">
      <div class="row" v-if="blockList.results">
        <div class="vloxBlockListContainer col-12 col-xl-6 p-1" v-for="block in blockList.results" :key="block.id">
          <button class="vloxBlockList my-2" v-on:click="addComponentToResource(block)">
            <div class="row h-100 align-items-center">
              <div class="col-4">
                <h5>{{block.title}}</h5>
                <p>{{block.description}}</p>
              </div>
              <div class="col-8 h-100">
                <img class="img-fluid blockPrev" src="" />
              </div>
            </div>
          </button>
        </div>
      </div>
    </div>
  </b-modal>
</template>

<script>
import axios from "axios";

export default {
  name: "add-content-modal",
  props: ['resinputid'],
  data() {
    return {
      blockList : [],
      resourceId: '',
    }
  },
  methods: {
    showAjaxError() {
      alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.')
    },
    addComponentToResource(blockData) {
      let finalObject = new Object();
      finalObject.title = blockData.title;
      finalObject.blockId = blockData.id;
      finalObject.properties = blockData.properties;
      finalObject.resourceId = this.resinputid;
      let axiosConfig = {
        headers: {
          'Content-Type': 'application/json',
          'User-Agent': 'Apache-HttpClient/4.1.1',
          "Access-Control-Allow-Origin": "*",
        }
      };
      const modalRef = this.$bvModal;
      // TODO reenable to use rigth api endpoint instead of this
      //  axios.post(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/Resources/'
      axios.post(window.location.protocol + "//" + window.location.host + this.$restRoute +
          '/rest/index.php?_rest=Resources',
          finalObject,
          axiosConfig)
          .then(response => {
            this.$emit('updated');
            modalRef.hide('add-content');
            document.getElementById('componentPreview').src =
                document.getElementById('componentPreview').src;
            console.log(response);
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    }
  },
  mounted() {
    axios.get(window.location.protocol + "//" + window.location.host +
                this.$restRoute + '/rest/index.php?_rest=blocks&limit=100')
        .then(response => {
          this.blockList = response.data;
        })
        .catch(error => {
          console.error(error);
          this.$dialog.alert('Error contacting webservice, check server logs!');
        });
  }
}
</script>

<style scoped>
.close {
  font-size: 1.5rem;
  padding: 5px 5px 10px;
  margin: 0;
  line-height: 13px;
  font-weight: bold;
  background: transparent;
  border-radius: 6px;
  border: 1px solid #afafaf;
}
.modal-body {
  background: #f8f8f8;
}
.vloxBlockListContainer {
  position: relative;
  height: 200px;
}
.vloxBlockList {
  width: 100%;
  height: 100%;
  background: no-repeat;
  border: 1px solid #b6b6b6;
  background-color: white;
  padding: 0;
}
.vloxBlockList h5, .vloxBlockList p {
  word-break: break-all;
  margin: 0 .5rem;
}
.vloxBlockList:hover {
  background-color: #ebebeb;
}
.blockPrev {
  background: #d6d6d6;
  height: 100%;
  width: 100%;
  display: inline-block;
}
.modal-dialog-scrollable .modal-body {
  overflow-x: hidden;
}
</style>