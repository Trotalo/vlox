<template>
  <b-row v-if="vloxList" cols="2" no-gutters>
    <b-col v-for="block in vloxList" :key="block.id" class="p-1">
      <b-card
          tag="article"
          class="overflow-hidden" style="max-width: 540px;">
        <b-row no-gutters>
          <b-col  md="6">
            <b-img @error="replaceByDefault" thumbnail fluid :src="componentImage(block.title)" alt="Image 1"></b-img>
          </b-col>
          <b-col md="6">
            <b-card-body class="text-center" :title="block.title">
              <b-card-text>
                {{block.description}}
              </b-card-text>
              <b-button v-on:click="selectBlock(block)"
                        variant="primary">{{buttonText}}</b-button>
              <br>
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
</template>

<script>

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
      return 'vlox/assets/components/vlox/compoSnapshots/' + componentName + '.png?rnd=' + this.cacheKey;
    },
    replaceByDefault(e) {
      e.target.src = 'vlox/assets/components/vlox/images/circulo.png';
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

</style>