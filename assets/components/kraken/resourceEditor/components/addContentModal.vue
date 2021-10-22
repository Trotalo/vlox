<template>
  <b-modal id="add-content" title="Add Block" size="xl" scrollable :hide-footer="true">
    <div class="content">
      <div class="row">
        <div class="krakenBlockListContainer col-12 col-xl-6 p-1" v-if="blockList.results" v-for="(block,index) in blockList.results">
          <button class="krakenBlockList my-2" v-on:click="addComponentToResource(block)">
            <div class="row h-100 align-items-center">
              <div class="col-4">
                <h5>{{block.title}}</h5>
                <p>{{block.description}}</p>
              </div>
              <div class="col-8 h-100">
                <img class="img-fluid blockPrev" src="" />
              </div>
            </div>
          </button>
        </div>
      </div>
    </div>
  </b-modal>
</template>

<script>
export default {
  name: "addContentModal",
  props: ['resInputId'],
  data() {
    return {
      blockList : [],
      resourceId: '',
    }
  },
  methods: {
    showAjaxError() {
      alert('The ajax petition has problem doing a GET request, please verify the blocks Controller.')
    },
    addComponentToResource(blockData) {
      let finalObject = new Object();
      finalObject.title = blockData.title;
      finalObject.blockId = blockData.id;
      finalObject.properties = blockData.properties;
      finalObject.resourceId = this.resInputId;
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
      axios.post(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/index.php?_rest=Resources',
          finalObject,
          axiosConfig)
          .then(response => {
            this.$emit('updated');
            modalRef.hide('add-content');
            document.getElementById('demoIframe').src = document.getElementById('demoIframe').src;
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    }
  },
  mounted() {
    axios.get(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/blocks?limit=100')
        .then(response => {
          this.blockList = response.data;
        })
        .catch(error => {
          this.showAjaxError();
        });
  }
}
</script>

<style scoped>
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
.modal-body {
  background: #f8f8f8;
}
.krakenBlockListContainer {
  position: relative;
  height: 200px;
}
.krakenBlockList {
  width: 100%;
  height: 100%;
  background: no-repeat;
  border: 1px solid #b6b6b6;
  background-color: white;
  padding: 0;
}
.krakenBlockList h5, .krakenBlockList p {
  word-break: break-all;
  margin: 0 .5rem;
}
.krakenBlockList:hover {
  background-color: #ebebeb;
}
.blockPrev {
  background: #d6d6d6;
  height: 100%;
  width: 100%;
  display: inline-block;
}
.modal-dialog-scrollable .modal-body {
  overflow-x: hidden;
}
</style>