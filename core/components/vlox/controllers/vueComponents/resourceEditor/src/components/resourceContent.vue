<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <div class="vloxBlock">
    <h5>{{vloxContent.title}}</h5>
    <p class="m-0">{{vloxContent.description}}</p>
    <button class="selectScrollArea" v-on:click="scrollToElement()"></button>
    <div class="iconsLeft">
      <b-icon-pencil-square class="btn editIcon" v-on:click="showEdit()"></b-icon-pencil-square>
      <b-modal
          v-bind:id="'edit-content' + vloxContent.id"
          v-bind:key="vloxContent.id"
          v-bind:ref="'edit-content' + vloxContent.id"
          @ok="save"
          title="Edit block" size="xl" scrollable>
        <content-editor
            :blockContent = "vloxContent"
            :id = "vloxContent.id">
        </content-editor>
      </b-modal>
<!--      <b-icon-eye class="btn showHideIcon mb-3"></b-icon-eye>-->
    </div>
    <b-icon-trash class="btn removeIcon mr-2" v-on:click="deleteElement()"></b-icon-trash>
    <b-icon-hand-index class="btn moveIcon"></b-icon-hand-index>
  </div>
</template>

<script>
import contentEditor from './contentEditor';
import axios from "axios";

export default {
  name: "resource-content",
  components: {
    'content-editor': contentEditor,
  },
  props: ['vloxContent'],
  methods: {
    showEdit() {
      if (this.$refs['edit-content' + this.vloxContent.id]) {
        this.$refs['edit-content' + this.vloxContent.id].show();
      } else {
        alert('Error opening: ' + 'edit-content' + this.vloxContent.id);
      }

    },
    deleteElement() {
      //First we confir that we actually want to delete the block
      //TODO this neds to be changed to use VueBoostrap's components
      const answer = confirm("You sure you want to proceed?");
      if (answer == true) {
        let finalObject = JSON.parse(JSON.stringify(this.vloxContent));
        finalObject['items'] = this.vloxContent['properties']['items'];
        delete finalObject['properties'];
        let axiosConfig = {
          headers: {
            'Content-Type': 'application/json',
            'User-Agent': 'Apache-HttpClient/4.1.1',
            "Access-Control-Allow-Origin": "*",
          }
        };
        // TODO reenable to use rigth api endpoint instead of this
        //  axios.post(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/Resources/'
        axios.delete(window.location.protocol + "//" + window.location.host + this.$restRoute +
            '/rest/index.php?_rest=Resources/'
            + this.vloxContent.id,
            finalObject,
            axiosConfig)
            .then(response => {
              this.$emit('updated');
              console.log(response);
            })
            .catch(error => {
              console.log(error);
              this.showErrorAjax();
            });
      }
    },
    save() {
      let finalObject = JSON.parse(JSON.stringify(this.vloxContent));
      finalObject['items'] = this.vloxContent['properties']['items'];
      delete finalObject['properties'];
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
          '/rest/index.php?_rest=Resources/'
          + this.vloxContent.id,
          finalObject,
          axiosConfig)
          .then(response => {
            modalRef.hide('add-content');
            /*document.getElementById('componentPreview').src =
                document.getElementById('componentPreview').src;*/
            console.log(response);
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    },
    scrollToElement() {
      /*const iframe = document.getElementById('componentPreview');
      var childDocument = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document;

      var selectedBlocks = childDocument.getElementsByClassName("blockSelected");
      while (selectedBlocks.length)
        selectedBlocks[0].className = selectedBlocks[0].className.replace(/\bblockSelected\b/g, "");

      const scrollToElement = childDocument.getElementById(this.vloxContent.id + '-' +
          this.vloxContent.title);
      if (!scrollToElement) {
        alert(this.vloxContent.id + '-' + this.vloxContent.title + " not present on DOM, please check your blocks!");
      } else {
        scrollToElement.className = "blockSelected";
        var rect = scrollToElement.getBoundingClientRect();
        childDocument.documentElement.scrollTop = rect.top;
      }*/
    }
  }
}
</script>

<style scoped>
.btn-outline-primary, .btn-check:focus+.btn, .btn:focus  {
  color: #525252;
  border-color: #525252;
}
.btn-check:focus+.btn, .btn:focus, .btn-check:active+.btn-outline-primary:focus, .btn-check:checked+.btn-outline-primary:focus, .btn-outline-primary.active:focus, .btn-outline-primary.dropdown-toggle.show:focus, .btn-outline-primary:active:focus {
  box-shadow: none !important;
}
.vloxBlock button {
  outline: none !important;
  border: 0 !important;
}
.vloxContainer, .vloxWrap {
  height: calc(100% - 35px);
  overflow-y: auto;
}
.vloxBlock {
  position: relative;
  padding: 0.5rem 3.2rem 0.5rem 48px;
  margin-bottom: .5rem;
  background: #f3f3f3;
  min-height: 5.5rem;
  overflow: hidden;
}
.vloxBlock h5 {
  position: absolute;
  top: 29px;
  width: 68%;
  line-height: 18px;
  font-size: 18px;
  margin: 0;
  overflow: hidden;
  max-height: 38px;
  transform: translateY(-50%);
}
.vloxBlock p {
  position: absolute;
  top: 64px;
  font-size: 14px;
  width: 72%;
  line-height: 14px;
  max-height: 30px;
  overflow: hidden;
  transform: translateY(-50%);
}
.vloxBlock button svg, .vloxBlock a svg{
  width: 20px;
  height: 20px;
  color: grey;
}
.removeIcon {
  position: absolute;
  right: 43px;
  top: 0;
}
.showHideIcon {
  position: absolute;
  bottom: 0;
  margin: 0;
}
.moveIcon {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: 0;
  background: #e8e8e8;
  height: 100%;
  width: 30px;
  padding: 0;
  color: #6b6b6b;
}
.editIcon {
  position: absolute;
  top: 0;
}
.iconsLeft {
  position: absolute;
  top: 5px;
  left: 2px;
  width: 33px;
  height: calc(100% - 10px);
  border-right: 1px solid lightgrey;
  text-align: start;
}
.vloxGhost {
  opacity: 0.5;
  background: #c1c1c1;
}
.vloxGhost h5, .vloxGhost p {
  color: black;
}
.selectScrollArea {
  position: absolute;
  width: calc(100% - 40px);
  height: 100%;
  top: 0;
  left: 0;
  border: navajowhite;
  background: transparent;
  padding: 0;
}

/*

.vloxWidth {
    max-width: 1980px;
}
button {
    border: none;
    outline: none;
}
.saveAddButtonsWrap {
    margin: 1rem 2.3rem 1rem 0;
}
.saveAddButtonsWrap button {
    background: none;
    border: 1px solid #b9b9b9;
    padding: .4rem 1.2rem;
}


 */
</style>