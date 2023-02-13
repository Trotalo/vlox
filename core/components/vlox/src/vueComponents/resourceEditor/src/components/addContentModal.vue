<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <b-modal id="add-content" title="Add Vlox (Click an item to add to current resource)" size="xl" scrollable :hide-footer="true">
    <div class="content">
      <div class="row" v-if="vloxList">
        <vlox-list-item
            :vlox-list="vloxList"
            :is-res-editor="true"
            @block-selected="addComponentToResource"
            button-text="Add to page"></vlox-list-item>
      </div>
    </div>
  </b-modal>
</template>

<script>
import axios from "axios";
import VloxListItem from "../../../shared/components/VloxListItem";
import Services from '@shared/services';
import {mapActions} from "vuex";

export default {
  name: "add-content-modal",
  components: {VloxListItem},
  props: ['resinputid'],
  data() {
    return {
      blockList : [],
      resourceId: '',
    }
  },
  computed: {
    vloxList(){
      return this.blockList.results ? this.blockList.results.filter(val => val.properties.type !== 1) : [];
    },
  },
  methods: {
    ...mapActions([
      'showLoading', 'hideLoading']),
    showAjaxError() {
      alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.')
    },
    async addComponentToResource(blockData) {
      let finalObject = new Object();
      finalObject.title = blockData.title;
      finalObject.blockId = blockData.id;
      finalObject.properties = blockData.properties;
      finalObject.resourceId = this.resinputid;
      //First we check if the vlox is already added to the
      this.showLoading();
      const isVloxOnRes = await Services.isVloxOnRes(finalObject.blockId, finalObject.resourceId);
      if (isVloxOnRes) {
        this.hideLoading();
        this.$dialog.alert("The current version doesn't allow the same vlox twice on the same resource");
      } else {
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
              this.hideLoading();
              this.$emit('updated');
              modalRef.hide('add-content');
              document.getElementById('componentPreview').src =
                  document.getElementById('componentPreview').src;
              console.log(response);
            })
            .catch(error => {
              this.hideLoading();
              console.log(error);
              this.showErrorAjax();
            });
      }
    }
  },
  mounted() {
    this.$root.$on('bv::modal::show', (/*bvEvent, modalId*/) => {
      //console.log('Opening', bvEvent, modalId)
      this.showLoading();
      axios.get(window.location.protocol + "//" + window.location.host +
        this.$restRoute + '/rest/index.php?_rest=blocks&limit=100')
        .then(response => {
          this.hideLoading();
          this.blockList = response.data;
        })
        .catch(error => {
          console.error(error);
          this.hideLoading();
          this.$dialog.alert('Error contacting webservice, check server logs!');
        });
    })
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