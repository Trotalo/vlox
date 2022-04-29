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
          <b-button size="sm" variant="success" class="mr-2">
            Add module
          </b-button>
        </div>
        <b-table striped hover :items="npmPackages" :fields="fields">
          <template #cell(actions)="data">
            <b-button size="sm" variant="danger" class="mr-2" @click="deleteModule(data)">
              Delete module
            </b-button>
          </template>
        </b-table>
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

export default {
  name: "baseConfiguration",
  components: {
    'vue-ace-editor': vueAceEditor,
  },
  data(){
    return {
      currentModules: [],
      mainJs: '',
      fields: ['name', 'version', 'actions'],
      newModuleName: '',
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
    deleteModule(modName) {
      this.$dialog
          .confirm('You really want to delete the package: ' + modName.item.name + '?')
          .then(function() {
            console.log('Clicked on proceed');
          })
          .catch(function() {
            console.log('Clicked on cancel');
          });
    },
    saveMainJs() {
      this.$dialog
          .confirm('Sure about the changes on main.js?')
          .then(async () => {
            await Services.saveMainJs(this.mainJs);
            console.log('Save main: ' + this.mainJs);
          })
          .catch(function() {
            console.log('Clicked on cancel');
          });
    }
  }
}
</script>

<style scoped>

</style>