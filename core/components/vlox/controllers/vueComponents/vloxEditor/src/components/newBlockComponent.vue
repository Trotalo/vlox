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
      ref="new-block" id="new-block" title="New VloX" size="xl" scrollable>
    <b-form class="smallForm">
      <b-form-group
          id="input-group-1"
          label="Block name"
          label-for="input-1">
        <validation-provider name="Name" rules="min:5|alpha_dash" v-slot="{ errors }">
          <b-form-input
              id="input-1"
              v-model="blockData.chunkName"
              placeholder="Enter at least 5 characters"
              :state="validName"
              required>
          </b-form-input>
          <span class="text-danger">{{ errors[0] }}</span>
        </validation-provider>
      </b-form-group>

      <b-form-group label="Select a Type" v-slot="{ ariaDescribedby }">
        <b-form-radio v-model="blockData.vloxType" :aria-describedby="ariaDescribedby" name="some-radios" :value="0">Vlox (full width content vlox to build pages)</b-form-radio>
        <b-form-radio v-model="blockData.vloxType" :aria-describedby="ariaDescribedby" name="some-radios" :value="1">Global Component (visual elements like buttons, cards and modals )</b-form-radio>
      </b-form-group>

      <b-form-group id="input-group-2" label="Blocks description:" label-for="input-2">
        <validation-provider name="Description" v-slot="{ errors }">
          <b-form-textarea
              id="textarea"
              v-model="blockData.description"
              placeholder="Enter a description for your block"
              rows="3"
              max-rows="6"
          ></b-form-textarea>
          <span class="text-danger">{{ errors[0] }}</span>
        </validation-provider>
      </b-form-group>
    </b-form>
    <template #modal-footer>
      <div class="addButton w-100">
        <b-button
            variant="outline-primary"
            class="addvloxBlock"
            @click="save(blockData)"
            type="button">Create Block
        </b-button>
      </div>
    </template>
  </b-modal>
</template>

<script>
import { ValidationProvider } from 'vee-validate';
import { extend } from 'vee-validate';
import { min, alpha_dash, alpha_num } from 'vee-validate/dist/rules';
import Services from "@shared/services";
import { mapActions } from 'vuex';

extend('min', min);
extend('alpha_dash', alpha_dash);
extend('alpha_num', alpha_num);


export default {
  name: "new-block-component",
  data() {
    return {
      blockData: {
        chunkName: '',
        description: '',
        vloxType: 0
      },
      validName: null,
      blockList: [],

    }
  },
  components: {
    'validation-provider': ValidationProvider
  },
  methods: {
    ...mapActions([
      'showLoading', 'hideLoading']),
    onSelectBlock (blockData) {
      this.blockData = blockData;
      //document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
    },
    async save(data) {
      const modalRef = this.$bvModal;
      try {
        this.showLoading();
        const existingChunk = await Services.getBlockData(this.blockData.chunkName);
        if (existingChunk.data.object) {
          this.hideLoading();
          await this.$dialog.alert('The block with name ' + this.blockData.chunkName +
              ' exists! please chose another name!');
        } else {
          const response = await Services.saveBlockData(data);
          const storedBlock = await Services.getBlockData(response.data.object.id);
          this.$emit('block-selected', storedBlock.data.object);
          this.$store.commit('change', true);
          this.blockData.chunkName = '';
          this.blockData.description = '';
          modalRef.hide('new-block');
          this.hideLoading();
        }
      } catch (e) {
        await this.$dialog.alert('Error saving block!');
        this.hideLoading();
        console.error(e);
      }
      console.log(data);
    },
  }
};
</script>
<style scope>

</style>