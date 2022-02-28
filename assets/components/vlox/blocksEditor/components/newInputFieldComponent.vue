<template>
  <b-modal ref="new-input" id="new-input" v-bind:title="'New Input Field' + ' for ' + $attrs.blockdatainput.chunkName"
           size="xl" scrollable @ok="save(inputData)">
    <b-form class="smallForm">

      <b-form-group
          id="input-group-1"
          label="Name Your Input"
          label-for="input-1"
          description="Tip: You can't have 2 or more different inputs with the same name."
          class="mb-3">
        <validation-provider name="Input name" rules="min:5|max:20|alpha_spaces" v-slot="{ errors }">
          <b-form-input
              id="input-1"
              v-model="inputData.name"
              placeholder="Only letters and spaces are allowed for this field!"
              :state="validName">
          </b-form-input>
          <span class="text-danger">{{ errors[0] }}</span>
        </validation-provider>
      </b-form-group>

      <b-form-group
          id="dropdown-group"
          label="Select an Input Type"
          description="Tip: You can't have 2 or more different inputs with the same name."
          class="mb-3">
        <b-form-select
            id="input-2"
            v-model="inputData.type"
            :options="options">
        </b-form-select>
      </b-form-group>

      <b-form-group
          id="input-group-3"
          label-for="input-3"
          v-slot="{ ariaDescribedby }">
        <validation-provider name="Default value" v-slot="{ errors }">
          <b-form-input
              v-if="inputData.type === 'text' || inputData.type === 'date' ||
              inputData.type === 'email' || inputData.type === 'number' ||
              inputData.type === 'url' || inputData.type === 'textarea' ||
              inputData.type === 'rich' || inputData.type === 'hidden' "
              id="textarea"
              v-model="inputData.value"
              placeholder="Enter a default value for your input">
          </b-form-input>

          <div v-else-if="inputData.type === 'checkbox' || inputData.type === 'radio'">
            <b-form-input
                id="checkboxName"
                placeholder="Enter your question text"
                v-model="optionTxt">
            </b-form-input>
            Add options:
            <b-button pill variant="success" @click="addOption()">+</b-button>

            <div v-for="item in inputData.options" :key="item">
              <b-form-checkbox
                  v-if="inputData.type === 'checkbox'"
                  id="checkbox-1"
                  name="checkbox-1"
                  value="accepted"
                  unchecked-value="not_accepted">
                <b-form-input id="checkboxName" placeholder="Option"></b-form-input>
              </b-form-checkbox>

              <b-form-radio
                  v-else-if="inputData.type === 'radio'"
                  :aria-describedby="ariaDescribedby" name="some-radios" value="A">
                <b-form-input id="checkboxName" placeholder="Option"></b-form-input>
              </b-form-radio>

              <b-button pill variant="danger">x</b-button>
            </div>
          </div>
          <b-form-file
              v-else-if="inputData.type === 'file' || inputData.type === 'image'"
              v-model="file1"
              :state="Boolean(file1)"
              placeholder="Choose a file or drop it here..."
              drop-placeholder="Drop file here...">
          </b-form-file>
          <span class="text-danger">{{ errors[0] }}</span>
        </validation-provider>
      </b-form-group>

    </b-form>
    <template #modal-footer>
      <div class="addButton w-100">
        <b-button
            variant="outline-primary"
            v-b-modal.new-input
            class="addvloxBlock"
            v-on:click="save(inputData)"
            type="button">Add Input
        </b-button>
      </div>
    </template>
  </b-modal>
</template>

<script>
// Register the component globally.
Vue.component('validation-provider', VeeValidate.ValidationProvider);

module.exports = {
  name: "new-input-component",
  props: ['blockDataInput'],
  data() {
    return {
      inputData: {
        name: '',
        value: '',
        type: null,
        options: []
      },
      validName: null,
      blockList: [],
      options: [
        {value: null, text: 'Select a type'},
        {value: 'checkbox', text: 'Checkbox'},
        {value: 'date', text: 'Date'},
        {value: 'email', text: 'Email'},
        {value: 'file', text: 'File'},
        {value: 'hidden', text: 'Hidden'},
        {value: 'image', text: 'Image'},
        {value: 'dropdown', text: 'Dropdown'},
        {value: 'number', text: 'Number'},
        {value: 'radio', text: 'Radio'},
        {value: 'rich', text: 'Richtext'},
        {value: 'text', text: 'Text'},
        {value: 'textarea', text: 'Textarea'},
        {value: 'url', text: 'URL'},
      ],
      file1: null,
      status: true,
      selected: '',
      optionTxt: ''
    }
  },
  methods: {
    save(data) {
      /*let axiosConfig = {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': '*',
        }
      };
      const modalRef = this.$bvModal;
      // TODO reenable to use rigth api endpoint instead of this
      axios.put(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/index.php?_rest=Blocks/',
          data,
          axiosConfig)
          .then(response => {
            modalRef.hide('add-content');
            //document.getElementById('demoIframe').src = document.getElementById('demoIframe').src;
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });*/

    },
    addOption(optionTxt) {
      this.inputData.options.push(optionTxt)
    }
  }
};
</script>
<style scope>
.custom-select {
  display: inline-block;
  width: 100%;
  height: calc(1.5em + .75rem + 2px);
  padding: .375rem 1.75rem .375rem .75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  vertical-align: middle;
  border: 1px solid #ced4da;
  border-radius: .25rem;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}
</style>