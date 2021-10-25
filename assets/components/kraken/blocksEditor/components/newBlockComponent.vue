<template>
  <b-modal ref="new-block" id="new-block" title="New KrakenBlock" size="xl" scrollable>
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

      <b-form-group id="input-group-2" label="Blocks description:" label-for="input-2">
        <validation-provider name="Description" rules="min:10|alpha_spaces" v-slot="{ errors }">
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
            class="addkrakenBlock"
            @click="save(blockData)"
            type="button">Create Block
        </b-button>
      </div>
    </template>
  </b-modal>
</template>

<script>
let blockList = [];
axios.get(window.location.protocol + "//" + window.location.host + Vue.prototype.$restRoute + '/rest/blocks')
    .then(response => {
      blockList = response.data;
    })
    .catch(error => {
      alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.');
    });

/*
Example of custom validation
VeeValidate.extend('description', {
  validate: (value) => (value && value.length > 9),
  message: 'Please add a description of at least 10 characters'
});*/
// Register the component globally.
Vue.component('validation-provider', VeeValidate.ValidationProvider);

module.exports = {
  name: "new-block-component",
  data() {
    return {
      blockData: {
        chunkName: '',
        description: ''
      },
      validName: null,
      blockList: [],
    }
  },
  methods: {
    onSelectBlock (blockData) {
      this.blockData = blockData;
      document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
    },
    save(data) {
      let axiosConfig = {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
        }
      };
      const modalRef = this.$bvModal;
      // TODO reenable to use rigth api endpoint instead of this
      axios.put(window.location.protocol + "//" + window.location.host + Vue.prototype.$restRoute + '/rest/index.php?_rest=Blocks/',
          data,
          axiosConfig)
          .then(response => {
            this.$emit('block-selected', response.data.object);
            //this.$root.$emit('reload-bocks-list', data);
            this.$store.commit('change', true);
            this.blockData.chunkName = '';
            this.blockData.description = '';
            modalRef.hide('new-block');

            //document.getElementById('demoIframe').src = document.getElementById('demoIframe').src;
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
      console.log(data);
    },
  }
};
</script>
<style scope>

</style>