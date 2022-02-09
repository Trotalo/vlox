<template>
  <b-modal
      v-model="showBlocksList"
      id="select-block" title="VloX List" size="xl" scrollable>
    <b-container fluid>
      <b-row>
        <b-col cols="12" lg="6" xl="4" v-if="blockList.results" v-for="(block,index) in blockList.results" :key="block.id">
          <div class="krakenBlock my-2">
            <button class="krakenBlockWSelect"
                    v-on:click="selectBlock(block)">
              <h5>{{ block.title }}</h5>
              <p>{{ block.description }}</p>
            </button>
            <button v-on:click="deleteBlock(block)" class="krakenBlockDelete btn btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
          </div>
        </b-col>
      </b-row>
    </b-container>
    <template #modal-footer>
      <div class="addButton w-100">
          <b-button
              variant="outline-primary"
              v-b-modal.new-block
              class="addkrakenBlock"
              @click="showBlocksList=false"
              type="button">Create a New Block
          </b-button>
      </div>
    </template>
  </b-modal>
</template>
<script>
module.exports = {
  name: "view-block-list",
  data() {
    return {
      showBlocksList: false,
      blockList : [],
      resourceId: 1,
      blockObject: []
    }
  },
  methods: {
    selectBlock(blockData) {
      const modalRef = this.$bvModal;
      axios.get(window.location.protocol + "//" + window.location.host + Vue.prototype.$restRoute +
          '/rest/blocks/' + blockData.id)
          .then(response => {
            this.blockObject = response.data.object;
            this.$emit('block-selected', this.blockObject);
            modalRef.hide('select-block');
          })
          .catch(error => {
            alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.');
          });
    },
    loadBLockList() {
      axios.get(window.location.protocol + "//" + window.location.host +
          Vue.prototype.$restRoute + '/rest/blocks?limit=100')
          .then(response => {
            this.blockList = response.data;
          })
          .catch(error => {
            console.error(error);
            this.$dialog.alert('Error contacting webservice, check server logs!');
          });
    },
    deleteBlock(block) {
      this.$dialog
          .confirm('Are your sure you want to delete ' + block.chunkName)
          .then(dialog => {
            axios.delete(window.location.protocol + "//" + window.location.host +
                          Vue.prototype.$restRoute + '/rest/blocks/' + block.id)
                .then(response => {
                  if (response.data.success) {
                    this.$dialog.alert('Block deleted successfully');
                    this.loadBLockList();
                  } else {
                    this.$dialog.alert(response.data.message);
                  }
                })
                .catch(error => {
                  this.$dialog.alert('Problems sending the petition: ' + error);
                });
          })
          .catch(function() {
            console.log('Clicked on cancel');
          });

    }
  },
  mounted() {
    this.loadBLockList();
  },
  beforeUpdate() {
    if (this.$store.getters.refreshList) {
      this.$store.commit('change', false);
      this.loadBLockList();
    }
  }
};
</script>
<style>
  #krakenAddBlockModal .modal-body button {
    background: #EEEEEE;
  }
  .krakenBlock {
    position: relative;
    height: 160px;
  }
  .krakenBlock .krakenBlockWSelect {
    width: 100%;
    height: 100%;
    padding: 2rem 0;
    background: no-repeat;
    border: 1px solid #b6b6b6;
    background-color: white;
  }
  .krakenBlock .krakenBlockWSelect:hover {
    background-color: #ebebeb;
  }
  .krakenBlock .krakenBlockDelete {
    position: absolute;
    right: 10px;
    top: 100%;
    transform: translateY(-121%);
  }
  .modal-body {
    background: #f8f8f8;
  }
  .addButton {
    text-align: center;
  }
  .close {
    font-size: 1.5rem;
    padding: 5px 5px 10px;
    margin: 0;
    line-height: 13px;
    font-weight: bold;
    background: transparent;
    border-radius: 6px;
    border: 1px solid #afafaf;
  }
  .btn-outline-danger, .btn-outline-success {
    color: #898989;
    border-color: #d8d8d8;
  }
  button {
    transition: 0.3s;
  }
</style>