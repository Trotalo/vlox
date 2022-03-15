<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <b-modal
      v-model="showBlocksList"
      id="select-block" title="VloX List" size="xl" scrollable>
    <b-container fluid>
      <b-row v-if="blockList.results">
        <b-col cols="12" lg="6" xl="4" v-for="block in blockList.results" :key="block.id">
          <div class="vloxBlock my-2">
            <button class="vloxBlockWSelect"
                    v-on:click="selectBlock(block)">
              <h5>{{ block.title }}</h5>
              <p>{{ block.description }}</p>
            </button>
            <button v-on:click="deleteBlock(block)" class="vloxBlockDelete btn btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
          </div>
        </b-col>
      </b-row>
    </b-container>
    <template #modal-footer>
      <div class="addButton w-100">
          <b-button
              variant="outline-primary"
              v-b-modal.new-block
              class="addvloxBlock"
              @click="showBlocksList=false"
              type="button">Create a New Block
          </b-button>
      </div>
    </template>
  </b-modal>
</template>
<script>
import axios from 'axios';

export default {
  name: "view-block-list",
  data() {
    return {
      showBlocksList: false,
      blockList: [],
      resourceId: 1,
      blockObject: [],
      axiosConfig: {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': "*",
        }
      }
    }
  },
  methods: {
    selectBlock(blockData) {
      const modalRef = this.$bvModal;
      axios.get(window.location.protocol + "//" + window.location.host + this.$restRoute +
          '/rest/index.php?_rest=blocks/' + blockData.id, this.axiosConfig)
          .then(response => {
            this.blockObject = response.data.object;
            this.$emit('block-selected', this.blockObject);
            modalRef.hide('select-block');
          })
          .catch(error => {
            alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.' +
                  JSON.stringify(error) );
          });
    },
    loadBLockList() {
      axios.get(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=blocks&limit=100', this.axiosConfig)
          .then(response => {
            this.blockList = response.data;
          })
          .catch(error => {
            console.error(error);
            this.$dialog.alert('Error contacting webservice, check server logs!');
          });
    },
    deleteBlock(block) {
      this.$dialog
          .confirm('Are your sure you want to delete ' + block.chunkName)
          .then(dialog => {
            console.log(dialog);
            axios.delete(window.location.protocol + "//" + window.location.host +
                          this.$restRoute + '/rest/blocks/' + block.id)
                .then(response => {
                  if (response.data.success) {
                    this.$dialog.alert('Block deleted successfully');
                    this.loadBLockList();
                  } else {
                    this.$dialog.alert(response.data.message);
                  }
                })
                .catch(error => {
                  this.$dialog.alert('Problems sending the petition: ' + error);
                });
          })
          .catch(function() {
            console.log('Clicked on cancel');
          });

    }
  },
  mounted() {
    this.loadBLockList();
  },
  beforeUpdate() {
    if (this.$store.getters.refreshList) {
      this.$store.commit('change', false);
      this.loadBLockList();
    }
  }
};
</script>
<style>
  #vloxAddBlockModal .modal-body button {
    background: #EEEEEE;
  }
  .vloxBlock {
    position: relative;
    height: 160px;
  }
  .vloxBlock .vloxBlockWSelect {
    width: 100%;
    height: 100%;
    padding: 2rem 0;
    background: no-repeat;
    border: 1px solid #b6b6b6;
    background-color: white;
  }
  .vloxBlock .vloxBlockWSelect:hover {
    background-color: #ebebeb;
  }
  .vloxBlock .vloxBlockDelete {
    position: absolute;
    right: 10px;
    top: 100%;
    transform: translateY(-121%);
  }
  .modal-body {
    background: #f8f8f8;
  }
  .addButton {
    text-align: center;
  }
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
  .btn-outline-danger, .btn-outline-success {
    color: #898989;
    border-color: #d8d8d8;
  }
  button {
    transition: 0.3s;
  }
</style>