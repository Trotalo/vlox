<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
<!--  <b-modal
      v-bind:id="'edit-content' + blockcontent.id"
      v-bind:key="blockcontent.id"
      v-bind:ref="'edit-content' + blockcontent.id"
      @ok="save"
      title="Edit block" size="xl" scrollable>-->
    <div class="row smallForm mb-2">
      <div class="col-12">
        <div class="row p-2 mb-2">
          <div class="col-12">
            <div class="py-1" v-if="blockcontent && blockcontent.properties">
              <div class="form-group" v-for="(item, index) in blockcontent.properties.items" :key="index">
                <div v-if="item.type === 'text'">
                  <label>{{item.name}}</label>
                  <b-form-input v-model="item.content" placeholder="item.name" class="mb-3"></b-form-input>
                </div>
                <div v-else-if="item.type === 'textArea'">
                  <h3>{{item.name}}</h3>
                  <textarea class="form-control" rows="6" v-model="item.content"></textarea>
                </div>
                <div v-else-if="item.type === 'object'">
<!--                  <div v-for="(arrayItem, index) in item.content" :key="index">
                    <div v-for="(internalField, index) in Object.keys(arrayItem)" :key="index">
                      <h3>{{internalField}}</h3>
                      <textarea class="form-control" rows="6" v-model="arrayItem[internalField]"></textarea>
                    </div>
                  </div>-->
                  <div id="task-config" class="accordion" role="tablist">
                    <h3>{{item.name}}</h3>
                    <div>
                      <b-button variant="success" @click="addItem(item)">Agregar item</b-button>
                    </div>
                    <b-card no-body class="mb-1" v-for="(arrayItem, index) in item.content" :key="index">
                      <b-card-header header-tag="header" class="p-1" role="tab">
                        <b-button :id="index" block v-b-toggle="['accordion' + index]"
                                  variant="info">{{'element ' + index}}</b-button>
                      </b-card-header>
                      <b-collapse :id="'accordion' + index"
                                  accordion="'my-acordion-' + index"
                                  role="tabpanel">
                        <b-card-body>
                          <div v-for="(internalField, index) in Object.keys(arrayItem)" :key="index">
                            <h3>{{internalField}}</h3>
                            <b-form-input class="form-control" rows="6" v-model="arrayItem[internalField]"></b-form-input>
                          </div>
                        </b-card-body>
                      </b-collapse>
                    </b-card>
                  </div>
                </div>
                <div class="row" v-else-if="item.type === 'image'">
                  <div class="col-6">
                    <h3>{{item.name}}</h3>
                    <input class="form-control" type="text" v-model="item.content"/>
                  </div>
                  <div class="col-6 w-100 h-100">
                    <img class="img-thumbnail w-100 h-100" :src="'../../../' + item.content">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<!--  </b-modal>-->
</template>

<script>
import axios from "axios";

export default {
  name: "content-editor",
  props: {
    blockcontent: Object,
    id: Number
  },
  methods: {
    save() {
      let finalObject = JSON.parse(JSON.stringify(this.blockcontent));
      finalObject['items'] = this.blockcontent['properties']['items'];
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
          '/rest/index.php?_rest=Resources/' + this.blockcontent.id,
          finalObject,
          axiosConfig)
          .then(response => {
            modalRef.hide('add-content');
            document.getElementById('componentPreview').src =
                document.getElementById('componentPreview').src;
            console.log(response);
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    },
    addItem(item){
      const cloneObj = Object.assign({}, item.content[0]);
      for (var member in cloneObj) cloneObj[member] = '';
      item.content.push(cloneObj);
    }
  },
  mounted() {
    console.log('zzzzzzzzzzzzzzzzz');
  }
}
</script>

<style scoped>
.smallForm {
  max-width: 500px;
  margin: 0 auto;
}
</style>