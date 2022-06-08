<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
  <b-modal id="project_config" title="Project configuration" size="xl" scrollable :hide-footer="true">
    <b-tabs content-class="mt-3">
      <b-tab title="npm Modules" active>
        <div>
          <b-form-input v-model="newModuleName" placeholder="Enter the module name"></b-form-input>
          <b-button @click="alterNpmModule(newModuleName, 0)" size="sm" variant="success" class="mr-2">
            Add module
          </b-button>
        </div>
        <b-table striped hover :items="npmPackages" :fields="fields">
          <template #cell(actions)="data">
            <b-button size="sm" variant="danger" class="mr-2" @click="alterNpmModule(data.item.name, 1)">
              Delete module
            </b-button>
          </template>
        </b-table>
        <b-modal id="npm-response-modal" size="xl" title="NPM Response" ok-only>
          <p class="log-view">{{npmResponse}}</p>
        </b-modal>
      </b-tab>
      <b-tab title="Main.js">
        <b-button size="sm" variant="success" class="mr-2" @click="saveMainJs()">
          Save
        </b-button>
        <vue-ace-editor v-model="mainJs" v-bind:options="htmlEdtOptions" id="editor1"/>
      </b-tab>
    </b-tabs>
  </b-modal>
</template>

<script>
import Services from '@shared/services';
import vueAceEditor from "@shared/components/vueAceEditor";
import { mapActions } from 'vuex';

export default {
  name: "baseConfiguration",
  props: ['resourceId'],
  components: {
    'vue-ace-editor': vueAceEditor,
  },
  data(){
    return {
      currentModules: [],
      mainJs: '',
      fields: ['name', 'version', 'actions'],
      newModuleName: '',
      npmResponse: '',
      htmlEdtOptions: {
        mode:'javascript',
        theme: 'monokai',
        fontSize: 11,
        fontFamily: 'monospace',
        highlightActiveLine: false,
        highlightGutterLine: false,
        newLineMode: "auto",
        foldStyle: "manual",
        maxLines: 500,
        minLines: 20,
        useSoftTabs: true,
        tabSize: 2
      },
    }
  },
  computed: {
    npmPackages: function () {
      if (this.currentModules.object) {
        return Object.keys(this.currentModules.object)
            .map((val)=> {
              return {
                'name': val,
                'version': this.currentModules.object[val]
              }
            });
      } else {
        return [];
      }
    },
  },
  async mounted() {
    this.currentModules = await Services.getNpmModules();
    const response = await Services.getMainJs();
    this.mainJs = response.object;
    console.log(this.currentModules);
  },
  methods: {
    ...mapActions([
      'showLoading', 'hideLoading']),
    saveMainJs() {
      this.$dialog
        .confirm('Sure about the changes on main.js?')
        .then(async () => {
          this.showLoading();
          await Services.saveMainJs(this.mainJs);
          this.hideLoading();
        })
        .catch(function(error) {
          console.error(error);
        });
    },
    alterNpmModule(module, action) {
      const actionText = (action === 0 ? 'Sure you want to add ' + module + ' module?'
                                        : 'Sure you want to remove ' + module + ' module?')
      this.$dialog
          .confirm(actionText)
          .then(async () => {
            this.showLoading();
            const response = await Services.modifyNpmModule(module, this.resourceId, action);
            this.currentModules = await Services.getNpmModules();
            this.hideLoading();
            if (!response.data.object) {
              this.$dialog.alert('Errors installing '
                  + module
                  + ' please check the name and try again');
            } else {
              this.npmResponse = response.data.object;
              this.$bvModal.show("npm-response-modal");
            }
            this.newModuleName = "";
          })
          .catch(function(error) {
            console.error(error);
          });
    }
  }
}
</script>

<style scoped>
.log-view{
  overflow-x: scroll;
  overflow-y: scroll;
  max-height: 75vh;
  white-space: pre;
}
</style>