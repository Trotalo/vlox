<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <section class="row vloxOptionView">
    <div class="col-8 col-xl-9">
      <div class="previewButtons">
        <button v-bind:class="[!renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(false)"><i class="fas fa-mobile-alt"></i> <span>576px</span></button>
        <button v-bind:class="[renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(true)"><i class="fas fa-desktop"></i> <span>1200px</span></button>
      </div>
      <div class="vloxPreview" :class="[renderDesktop ? 'previewDesktop' : 'previewMobile']">
        <iframe id="demoIframe"
                :src="frontPreview"
                sandbox="allow-same-origin allow-forms allow-scripts"
                style="width: 100%;height: 100%">
        </iframe>
      </div>
    </div>

    <div class="col-4 col-xl-3">
      <div class="mb-4 saveAddButtonsWrap">
        <div class="col-12">
          <button class="btn btn-outline-primary vloxSaveDraft">Save Draft</button>
          <b-button
              v-b-modal.add-content
              class="btn btn-success addvloxBlock"
              type="button">Add Block
          </b-button>
          <add-content-modal v-bind:resinputid="resourceId"
                                    @updated="updated()">
          </add-content-modal>
<!--          <b-modal id="add-content" title="Add Block" size="xl" scrollable :hide-footer="true">
            <vlox-block-add-content v-bind:resInputId="resourceId"
                                      @updated="updated()">
            </vlox-block-add-content>
          </b-modal>-->
        </div>
      </div>
      <div class="vloxContainer">
        <div v-if="resultsList"
             class="col-12 vloxWrap">
          <draggable
              ghost-class="vloxGhost"
              v-model="resultsList"
              :component-data="getComponentData()">
            <resource-content
                v-for="blockSection in resultsList"
                :key="blockSection.id"
                :vloxContent = "blockSection"
                @updated="updated()">
            </resource-content>
          </draggable>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";
import resourceContent from "./resourceContent";
import addContentModal from "./addContentModal";
import draggable from 'vuedraggable'

export default {
  name: "resource-editor",
  components: {
    'resource-content': resourceContent,
    'add-content-modal': addContentModal,
    draggable
  },
  data() {
    return {
      //TODO este id se debe cargar de forma dinamica
      resourceId: '',
      frontPreview: '',
      resultsList: [],
      renderDesktop: true
    }
  },
  methods: {
    setUpSizePreview(render) {
      this.renderDesktop = render;
    },
    showErrorAjax() {
      alert('The ajax petition has problem doing a GET request, please verify the Resources Controller.');
    },
    async loadData() {
      //TODO aca se debe pasar el id para hacer la consulta
      //this.resourceId = 28;
      let response = await axios.get(window.location.protocol + "//" + window.location.host +
          this.$restRoute + '/rest/index.php?_rest=Resources/' + this.resourceId)
          //.then(response => {
      if (response) {
        this.resultsList = response.data.results;
        this.resultsList.sort((a, b) => {
          if (a.position < b.position) {
            return -1;
          }
          if (a.position > b.position) {
            return 1;
          }
          return 0;
        });
        console.log(this.resultsList);
      } else {
        this.$dialog.alert('Problems reaching the webservice!');
      }

          /*})
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });*/
    },
    updated() {
      this.loadData();
    },
    handleChange() {
      this.resultsList.forEach((element, index) => {
        element['position'] = index + 1;
      });
      //Now we send the data to the server!!!
      let axiosConfig = {
        headers: {
          'Content-Type': 'application/json',
          "Access-Control-Allow-Origin": "*",
        }
      };
      //TODO revisar este put aca esta mandando todo lo que cargo
      axios.put(window.location.protocol + "//" + window.location.host + this.$restRoute +
          '/rest/index.php?_rest=Resources',
          this.resultsList,
          axiosConfig)
          .then(response => {
            document.getElementById('demoIframe').src = document.getElementById('demoIframe').src;
            console.log(response);
          })
          .catch(error => {
            console.error(error);
            this.showErrorAjax();
          });
    },
    inputChanged(value) {
      console.log('changed: ' + value);
    },
    getComponentData() {
      return {
        on: {
          end: this.handleChange,
          input: this.inputChanged
        },
        attrs:{
          wrap: true
        },
        props: {
          value: this.resultsList
        }
      };
    }
  },
  async beforeMount() {
    debugger;
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const resId = urlParams.get("resId");
    //this.frontPreview = window.location.protocol + "//" + window.location.host + "/index.php?id=" + resId;
    this.frontPreview = "www.google.com";
    this.resourceId = resId;
    await this.loadData();
  }
}
</script>

<style scoped>
html, body, #app, .vloxOptionView{
  height: 100%;
  overflow: hidden;
  margin: 0;
}
.btn {
  margin-bottom: .5rem;
  text-transform: initial;
  font-weight: bold;
  font-size: .8rem;
  padding: 8px;
  text-transform: uppercase;
}
.btn-success:focus {
  color: white !important;
}
.btn-outline-primary, .btn-check:focus+.btn, .btn:focus  {
  color: #525252;
  border-color: #525252;
}
.btn-check:focus+.btn, .btn:focus, .btn-check:active+.btn-outline-primary:focus, .btn-check:checked+.btn-outline-primary:focus, .btn-outline-primary.active:focus, .btn-outline-primary.dropdown-toggle.show:focus, .btn-outline-primary:active:focus {
  box-shadow: none !important;
}
.btn-outline-primary:hover {
  color: white;
  background-color: #f8a000;
  border-color: #b07100;
}
.vloxWidth {
  max-width: 2030px;
}
button, button i:after, button span {
  transition: .5s !important;
}
.vloxOptionView .col-8 {
  background: #d4d4d4;
  padding: 0;
}
.vloxOptionView .col-8, .vloxOptionView .col-4 {
  height: 90vh;
}
.previewButtons {
  text-align: center;
  margin-top: .5rem;
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
  color: #6c757d;
  font-size: .8rem;
  font-weight: normal;
}
.vloxPreview {
  position: relative;
  overflow: hidden;
  width: 97%;
  padding-top: 56.25%;
  margin: 1rem auto;
  height: calc(100% - 5rem);

  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
}
.previewButtons .icon-active, .previewButtons .icon-active span, .previewButtons .icon-active i {
  color: white;
  -webkit-transition: all 0.25s ease-in-out;
  -moz-transition: all 0.25s ease-in-out;
  -o-transition: all 0.25s ease-in-out;
  -ms-transition: all 0.25s ease-in-out;
  transition: all 0.25s ease-in-out;
}
.previewDesktop {
  max-width: 1200px;
}
.previewMobile {
  max-width: 576px;
}
.previewDesktop iframe, .previewMobile iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  margin: 0 auto;
}
.addvloxBlock {
  float: right;
}
.blockSelected {
  border: 2px solid red;
}
</style>