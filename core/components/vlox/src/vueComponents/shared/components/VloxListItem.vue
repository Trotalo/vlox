<template>
  <b-row v-if="vloxList && !isResEditor" cols="2" no-gutters>
    <b-col v-for="block in vloxList" :key="block.id" class="p-1">
      <b-card
          tag="article"
          :title="block.title"
          class="overflow-hidden" style="max-width: 540px;">
        <b-row no-gutters>
          <b-col  md="6">
<!--            <b-img @error="replaceByDefault" thumbnail fluid :src="componentImage(block.title)" alt="Image 1"></b-img>-->
          </b-col>
          <b-col md="6">
            <b-card-body class="text-center">
              <b-button v-on:click="selectBlock(block)"
                        variant="primary">{{buttonText}}</b-button>
              <br>
              <b-icon-trash
                  variant="danger"
                  v-on:click.stop="deleteBlock(block)"></b-icon-trash>
            </b-card-body>
          </b-col>
        </b-row>
      </b-card>
    </b-col>
  </b-row>
  <b-row v-else-if="vloxList && isResEditor" cols="2" no-gutters>
    <b-col v-for="block in vloxList" :key="block.id" class="p-1">
      <b-card
          tag="article"
          :title="block.title"
          class="overflow-hidden" style="max-width: 540px;"
          @click="selectBlock(block)">
        <b-row no-gutters>
          <b-card-body class="text-center">
            <b-img @error="replaceByDefault" thumbnail fluid :src="componentImage(block.title)" alt="Image 1"></b-img>
          </b-card-body>
        </b-row>
      </b-card>
    </b-col>


  </b-row>
</template>

<script>
import Vue from "vue";

export default {
  name: "VloxListItem",
  data() {
    return {
      cacheKey: +new Date(),
      interval: null
    }
  },
  props: {
    vloxList: {
      type: Array,
      required: true
    },
    buttonText: {
      type: String,
      required: true
    },
    isResEditor: {
      type: Boolean,
      default: false
    }
  },
  methods: {
    selectBlock(blockData) {
      //first we stop the server
      this.$emit('block-selected', blockData);
    },
    deleteBlock(block) {
      this.$emit('delete', block);
    },
    componentImage(componentName) {
      //TODO see for way to improve reloading
      //return Vue.prototype.$restRoute + '/compoSnapshots/' + componentName + '.png?rnd=' + this.cacheKey;
      return Vue.prototype.$restRoute + '/compoSnapshots/' + componentName + '.png';
    },
    replaceByDefault(e) {
      e.target.src = Vue.prototype.$restRoute + '/images/circulo.png';
    },
    doesFileExist(urlToFile) {
      var xhr = new XMLHttpRequest();
      xhr.open('HEAD', urlToFile, false);
      xhr.send();

      return xhr.status !== 404;
    }
  },
  created() {
    this.interval = setInterval(() => {
      this.cacheKey = +new Date();
    }, 60 * 1000);
  },
  destroyed() {
    clearInterval(this.interval);
  },
}
</script>

<style scoped>
.card-title{
  white-space: nowrap;
  overflow: hidden;
}
</style>