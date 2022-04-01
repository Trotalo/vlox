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
      <b-tab title="Main.js"><p>Im the main.js file!</p></b-tab>
    </b-tabs>
  </b-modal>
</template>

<script>
import Services from '@shared/services';

let services = new Services();

export default {
  name: "baseConfiguration",
  data(){
    return {
      currentModules: [],
      fields: ['name', 'version', 'actions'],
      newModuleName: '',
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
    this.currentModules = await services.getNpmModules();
    console.log(this.currentModules);
  },
  methods: {
    deleteModule(modName) {
      debugger;
      this.$dialog
          .confirm('You really want to delete the package: ' + modName.item.name + '?')
          .then(function() {
            console.log('Clicked on proceed');
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