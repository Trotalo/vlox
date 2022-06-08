<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <div>
    <b-container class="mt-4">
      <b-row class="align-items-center">
        <b-col cols="12" lg="3" class="mb-3">
          <img v-if="showControls" src="./images/vlox.png" class="vloxLogo" />
        </b-col>
        <b-col v-if="showControls" cols="12" md="6" lg="5">
          <b-button
              variant="outline-primary"
              v-b-modal.new-block
              class="addvloxBlock"
              type="button">Create a New Block
          </b-button>
          <new-block-component v-on:block-selected="onSelectBlock"></new-block-component>
          <b-button
              variant="outline-primary"
              v-b-modal.select-block
              class="addvloxBlock"
              type="button">Select A Block
          </b-button>
        </b-col>
<!--        <b-col v-if="showControls" cols="12" md="6" lg="4" class="text-right">
          <b-button variant="success" v-on:click="save()">Save & Publish</b-button>
        </b-col>-->
        <view-block-list v-on:block-selected="onSelectBlock"></view-block-list>
        <!--      <b-col class="mt-4 mt-lg-0">-->
        <!--        <h5 v-if="showControls" class="lightGrey">Preview Area for {{blockData.chunkName}}</h5>-->
        <!--      </b-col>-->
      </b-row>
    </b-container>

    <b-container fluid v-bind:class="{ previewBackground: showControls }">
      <h1 v-if="blockData">Editing: {{blockData.chunkName}}</h1>
      <div class="previewButtons d-none d-md-block" v-if="showControls">
        <button v-bind:class="[!renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(false)"><i class="fas fa-mobile-alt"></i> <span>576px</span></button>
        <button v-bind:class="[renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(true)"><i class="fas fa-desktop"></i> <span>1200px</span></button>
      </div>
<!--      <server-control v-if="showControls"
                      :save-method="save"
                      :resource-id="31"></server-control>-->
      <b-row class="previewAreaWidth" :class="[renderDesktop ? 'previewDesktop' : 'previewMobile']">
        <b-col v-bind:class="{ previewExtraHeight: !showControls }" cols="12" class="previewArea">
          <div v-if="showControls">
            <iframe id="componentPreview"
                    :src="localAddress"
                    style="
            width: 100%;
            height: 100%;
        "></iframe>
            <br>
            <b-button :href="localAddress" target="_blank">Open on new window</b-button>
          </div>
          <div v-else class="firstScreen">
            <new-block-component v-on:block-selected="onSelectBlock"></new-block-component>
            <img src="./images/vlox.png" />
            <b-button
                variant="outline-primary"
                v-b-modal.select-block
                class="addvloxBlockBig"
                type="button">View your VloX!
            </b-button>
          </div>
        </b-col>
      </b-row>
      <div v-if="!showControls" class="modXMonsterLogo">
        <a href="https://trotalo.com" target="_blank">
          <img class="img-fluid" src="./images/logoMenu.svg" />
        </a>
        <p>All Rights Reserved © 2020</p>
      </div>
    </b-container>

    <b-container v-if="showControls">
      <server-control v-on:save-method="save"
                      :resource-id="rendererId"
                      :block-data="blockData"
                      :vlox-type="0"></server-control>
      <b-row class="mt-4">
        <b-tabs content-class="mt-2 position-relative" class="col-12">
          <b-tab title="Code Editor" active>
            <b-row class="codeEditorBlocks mb-3">
              <b-col id="htmlEditor" cols="12" md="12">
                <h3>HTML</h3>
                <vue-ace-editor v-model="blockData.htmlSection" v-bind:options="htmlEdtOptions" id="editor1"/>
              </b-col>
            </b-row>
          </b-tab>
        </b-tabs>
      </b-row>
    </b-container>
  </div>
</template>

<script>
import viewBlocksList from './viewBlocksList'
import newBlockComponent from './newBlockComponent';
import vueAceEditor from "@shared/components/vueAceEditor";
import ServerControl from '../../../shared/components/ServerControl';
import Services from '@shared/services';

import axios from 'axios';

const axiosConfig = {
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': "*",
  }
}

export default {
  name: "editor-home",
  components: {
    'view-block-list': viewBlocksList,
    'new-block-component': newBlockComponent,
    'vue-ace-editor': vueAceEditor,
    'server-control': ServerControl
  },
  data() {
    return {
      showBlocksList: false,
      fields: ['name', 'content', 'type' ,'controls'],
      items: [],
      blockData: [],
      showInput: false,
      showControls: false,
      inputFieldData: {},
      localAddress: window.location.protocol + "//" + window.location.hostname + ':8080',
      renderDesktop: true,
      rendererId: 0,
      //Ace editor section
      //  options object
      //  https://github.com/ajaxorg/ace/wiki/Configuring-Ace
      htmlEdtOptions: {
        mode:'html',
        theme: 'monokai',
        fontSize: 11,
        fontFamily: 'monospace',
        highlightActiveLine: false,
        highlightGutterLine: false,
        newLineMode: "auto",
        foldStyle: "manual",
        maxLines: 50,
        minLines: 50,
        useSoftTabs: true,
        tabSize: 2
      },
    }
  },
  methods: {
    setUpSizePreview(render) {
      this.renderDesktop = render;
    },
    onSelectBlock (blockData) {
      this.blockData = blockData;
      this.showControls = true;
      //we initializae the data table
      /*const blockContents =  blockData.properties.items instanceof Array ?
          blockData.properties.items:
          JSON.parse(blockData.properties).items;
      this.items = blockContents;//[];*/
      /*blockContents.forEach(element => {
        this.items.push({ variable_name: element.name, default_value: element.content, variable_type: element.type});
      });*/
      if (document.getElementById('componentPreview')) {
        document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
      }
    },
    addInputField() {
      this.items.push({content: "Input a value", name: "Input a name", type: "text"});
    },
    onRowSelected(items) {
      if (items !== undefined && items.length > 0) {
        this.inputFieldData = items[0];
        this.showInput = true;
      }
    },
    saveInput(){
      this.showInput = false;
    },
    cancelEdit() {
      this.showInput = false;
    },
    save() {
      const modalRef = this.$bvModal;
      // TODO reenable to use rigth api endpoint instead of this
      //  axios.post(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/Resources/'
      axios.put(window.location.protocol + "//" + window.location.host +
                  this.$restRoute + '/rest/index.php?_rest=Blocks/'
          + this.blockData.id,
          this.blockData,
          axiosConfig)
          .then(response => {
            console.log(response);
            modalRef.hide('add-content');
            document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    },
    highlighter(code) {
      console.log(code);
      // js highlight example
      //return Prism.highlight(code, Prism.languages.js, "js");
    },
  },
  updated() {
    if (document.getElementById("editor1") && !this.loaded) {
      this.loaded = true;
    }
  },
  async beforeMount() {
    //read the id parameter
    //const response = await services.getRendererId();
    const response = await Services.getRendererId();
    this.rendererId = response.object;
    console.log(this.rendererId);

  }

};
</script>
<style scoped lang="scss">
h3 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #636363;
  padding: .2rem;
}
.previewAreaWidth {
  max-width: 1320px;
  margin: 0 auto;

  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
}
.previewDesktop {
  max-width: 1200px;
}
.previewMobile {
  max-width: 576px;
}
.previewBackground {
  background-color: #d4d4d4;
  padding: .25rem 0;
}
.previewArea {
  overflow: hidden;
  /*height: 260px;*/
  height: 50vh;
  resize: both;
  border-top: 1px solid #c8c9ca;
  padding: 0;
  border-bottom: 1px solid #c8c9ca;
}
.previewExtraHeight {
  height: 400px;
  border: none;
  height: calc(100vh - 80px);
}
.firstScreen {
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -60%);
}
.firstScreen img {
  height: 100%;
  max-height: 160px;
  display: block;
}
.modXMonsterLogo {
  position: absolute;
  bottom: 2rem;
  right: 3rem;
}
.modXMonsterLogo img {
  max-height: 70px;
  margin: 0 0 .5rem 0;
}
.modXMonsterLogo p {
  text-align: center;
  margin: 0;
  font-weight: 600;
  color: #848484;
  font-size: .9rem;
}
.my-editor {
  resize: both;
}
td {
  padding: .7rem .6rem .6rem !important;
  position: relative;
}
td p {
  margin-bottom: 0;
}
td .btn {
  padding: 2px 16px !important;
  margin: 0 6px;
}
td button.dropdown-toggle {
  margin: 3px;
}
td button i {
  font-size: .9rem;
}
.form-control {
  font-size: 1rem;
  padding: 5px 10px;
}
td .btn-group button {
  margin-top: 3px;
}
.table>:not(caption)>*>* {
  box-shadow: inset 0 0 0 0 var(--bs-table-accent-bg);
}
.table.b-table > tbody > .table-active, .table.b-table > tbody > .table-active > th, .table.b-table > tbody > .table-active > td {
  background-color: #eeeeee;
}
.addvloxBlockBig {
  margin: 2rem auto 0;
  padding: 1.3rem 3rem;
  background: whitesmoke;
}
.tabs {
  width: 100%;
}
.updatePrev {
  position: absolute;
  right: 10px;
  top: -2px;
  z-index: 1;
}
.previewButtons {
  text-align: center;
  margin: .1rem auto .5rem;
  max-width: 1200px;
}
.previewButtons h5 {
  color: #737373;
  position: absolute;
  font-size: 1.2rem;
  font-weight: 500;
  margin: .15rem;
}
.previewButtons button {
  background: transparent;
  border: none;
  padding: 0;
  margin: 0 0 0 1rem;
  width: auto;
  color: #6c757d;
}
.previewButtons button:first-child {
  margin: 0;
}
.previewButtons span {
  font-size: .8rem;
  font-weight: normal;
}
.previewButtons .icon-active, .previewButtons .icon-active span {
  color: white;
  -webkit-transition: all 0.25s ease-in-out;
  -moz-transition: all 0.25s ease-in-out;
  -o-transition: all 0.25s ease-in-out;
  -ms-transition: all 0.25s ease-in-out;
  transition: all 0.25s ease-in-out;
}
</style>