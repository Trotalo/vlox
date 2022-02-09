<template>
  <div>
    <b-container class="mt-4">
      <b-row class="align-items-center">
        <b-col cols="12" lg="3" class="mb-3">
          <img v-if="showControls" src="./images/ModXMonster-logo.png" class="krakenLogo" />
        </b-col>
        <b-col v-if="showControls" cols="12" md="6" lg="5">
          <b-button
              variant="outline-primary"
              v-b-modal.new-block
              class="addkrakenBlock"
              type="button">Create a New Block
          </b-button>
          <new-block-component v-on:block-selected="onSelectBlock"></new-block-component>
          <b-button
              variant="outline-primary"
              v-b-modal.select-block
              class="addkrakenBlock"
              type="button">Select A Block
          </b-button>
        </b-col>
        <b-col v-if="showControls" cols="12" md="6" lg="4" class="text-right">
          <b-button variant="success" v-on:click="save()">Save & Publish</b-button>
        </b-col>
        <view-block-list v-on:block-selected="onSelectBlock"></view-block-list>
        <!--      <b-col class="mt-4 mt-lg-0">-->
        <!--        <h5 v-if="showControls" class="lightGrey">Preview Area for {{blockData.chunkName}}</h5>-->
        <!--      </b-col>-->
      </b-row>
    </b-container>

    <b-container fluid v-bind:class="{ previewBackground: showControls }">
      <div class="previewButtons d-none d-md-block" v-if="showControls">
        <h5 v-if="showControls">{{blockData.chunkName}} Block</h5>
        <button v-bind:class="[!renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(false)"><i class="fas fa-mobile-alt"></i> <span>576px</span></button>
        <button v-bind:class="[renderDesktop ? 'icon-active' : 'icon', 'btn']" v-on:click="setUpSizePreview(true)"><i class="fas fa-desktop"></i> <span>1200px</span></button>
      </div>
      <b-row class="previewAreaWidth" :class="[renderDesktop ? 'previewDesktop' : 'previewMobile']">
        <b-col v-bind:class="{ previewExtraHeight: !showControls }" cols="12" class="previewArea">
          <iframe v-if="showControls" id="componentPreview"
                  :src="localAddress"
                  style="
            width: 100%;
            height: 100%;
        "></iframe>
          <div v-else class="firstScreen">
            <new-block-component v-on:block-selected="onSelectBlock"></new-block-component>
            <img src="./images/ModXMonster-logo.png" />
            <b-button
                variant="outline-primary"
                v-b-modal.select-block
                class="addkrakenBlockBig"
                type="button">Release the Kraken!
            </b-button>
          </div>
        </b-col>
      </b-row>
      <div v-if="!showControls" class="modXMonsterLogo">
        <a href="https://modxmonster.com" target="_blank">
          <img class="img-fluid" src="./images/ModXMonster-logoH.png" />
        </a>
        <p>All Rights Reserved © 2020</p>
      </div>
    </b-container>

    <b-container v-if="showControls">
      <b-row class="mt-4">
        <b-tabs content-class="mt-2 position-relative" class="col-12">
          <!--b-tab title="Input Fields">
            <b-row>
              <b-col cols="12" class="text-right">
                <b-button
                    variant="outline-primary"
                    v-b-modal.new-input
                    class="addkrakenBlock"
                    type="button">Add Input Field
                </b-button>
                <new-input-component v-bind:blockDataInput="blockData"></new-input-component>
              </b-col>
              <b-col>
                <b-table :items="items"
                         :fields="fields"
                         select-mode="single"
                         selectable
                         @row-selected="onRowSelected">
                  <template #cell(name)="row">
                    <b-form-input v-if="showInput && row.item.name === inputFieldData.name" v-model="row.value"  placeholder="Enter your name"></b-form-input>
                    <p v-else>{{row.value}}</p>
                  </template>
                  <template #cell(content)="row">
                    <b-form-input v-if="showInput && row.item.name === inputFieldData.name" v-model="row.value"  placeholder="Enter your name"></b-form-input>
                    <p v-else>{{row.value}}</p>
                  </template>
                  <template #cell(type)="row">
                    <b-dropdown v-if="showInput && row.item.name === inputFieldData.name" text="Input type" variant="light">
                      <b-dropdown-item>Checkbox</b-dropdown-item>
                      <b-dropdown-item>Date</b-dropdown-item>
                      <b-dropdown-item>Email</b-dropdown-item>
                      <b-dropdown-item>File</b-dropdown-item>
                      <b-dropdown-item>Hidden</b-dropdown-item>
                      <b-dropdown-item>Image</b-dropdown-item>
                      <b-dropdown-item>Dropdown</b-dropdown-item>
                      <b-dropdown-item>Number</b-dropdown-item>
                      <b-dropdown-item>Radio</b-dropdown-item>
                      <b-dropdown-item>Richtext</b-dropdown-item>
                      <b-dropdown-item>Text</b-dropdown-item>
                      <b-dropdown-item>Textarea</b-dropdown-item>
                      <b-dropdown-item>URL</b-dropdown-item>
                    </b-dropdown>
                    <p v-else>{{row.value}}</p>
                  </template>
                  <template #cell(controls)="row">
                    <b-button-group v-if="showInput === true && row.item.name === inputFieldData.name">
                      <b-button variant="outline-danger" v-on:click="cancelEdit()"><i class="fas fa-times"></i></b-button>
                      <b-button variant="outline-success" v-on:click="saveInput()"><i class="fas fa-check"></i></b-button>
                    </b-button-group>
                    <b-button v-else variant="outline-danger"><i class="fas fa-trash-alt"></i></b-button>
                  </template>
                </b-table>
              </b-col>
            </b-row>
          </b-tab-->
          <b-tab title="Code Editor" active>
            <b-row class="codeEditorBlocks mb-3">
              <b-col id="htmlEditor" cols="12" md="12">
                <h3>HTML</h3>
                <b-button variant="outline-primary" v-on:click="save()" class="updatePrev">Update Preview</b-button>
                <vue-ace-editor v-model="blockData.htmlSection" v-bind:options="htmlEdtOptions" id="editor1"/>
              </b-col>
<!--              <b-col id="styleEditor" cols="12" md="6">
                <h3>SCSS</h3>
                <vue-ace-editor v-model="blockData.styleSection" v-bind:options="styleEdtOptions" id="editor2"/>
              </b-col>
              <b-col id="jsEditor" cols="12" md="6" class="mb-3">
                <h3>Vue <small>V2.6.2</small></h3>
                <vue-ace-editor v-model="blockData.scriptSection" v-bind:options="codeEdtOptions" id="editor3"/>
              </b-col>-->
            </b-row>
          </b-tab>
        </b-tabs>
      </b-row>
    </b-container>
  </div>
</template>

<script>
const VueAceEditor = {
  //  simplified model handling using `value` prop and `input` event for $emit
  props:['value','id','options'],
  model: {
    prop: 'value',
    event: 'listchange'
  },
  computed: {
    valueLocal: {
      get: function() {
        return this.value
      },
      set: function(value) {
        this.$emit('listchange', value)
      }
    }
  },

  //  add dynmic class and id (if not set) based on component tag
  template:`
    <div :id="id ? id: $options._componentTag +'-'+ _uid"
         :class="$options._componentTag">
    <slot></slot>
    </div>
  `,

  watch:{
    value() {
      //  two way binding – emit changes to parent
      this.$emit('input', this.value);

      //  update value on external model changes
      if(this.oldValue !== this.value){
        this.editor.setValue(this.value, 1);
      }
    }
  },

  mounted(){
    //  editor
    this.editor = window.ace.edit(this.$el.id);

    //  deprecation fix
    this.editor.$blockScrolling = Infinity;

    //  ignore doctype warnings
    const session = this.editor.getSession();
    session.on("changeAnnotation", () => {
      const a = session.getAnnotations();
      const b = a.slice(0).filter( (item) => item.text.indexOf('DOC') == -1 );
      if(a.length > b.length) session.setAnnotations(b);
    });

    //  editor options
    //  https://github.com/ajaxorg/ace/wiki/Configuring-Ace
    this.options = this.options || {};

    //  opinionated option defaults
    this.options.maxLines = this.options.maxLines || Infinity;
    this.options.printMargin = this.options.printMargin || false;
    this.options.highlightActiveLine = this.options.highlightActiveLine || false;

    //  hide cursor
    if(this.options.cursor === 'none' || this.options.cursor === false){
      this.editor.renderer.$cursorLayer.element.style.display = 'none';
      delete this.options.cursor;
    }

    //  add missing mode and theme paths
    if(this.options.mode && this.options.mode.indexOf('ace/mode/')===-1) {
      this.options.mode = `ace/mode/${this.options.mode}`;
    }
    if(this.options.theme && this.options.theme.indexOf('ace/theme/')===-1) {
      this.options.theme = `ace/theme/${this.options.theme}`;
    }
    this.editor.setOptions(this.options);


    //  set model value
    //  if no model value found – use slot content
    if(!this.value || this.value === ''){
      this.$emit('input', this.editor.getValue());
    } else {
      this.editor.setValue(this.value, -1);
    }

    //  editor value changes
    this.editor.on('change', () => {
      //  oldValue set to prevent internal updates
      this.valueLocal = this.editor.getValue();
      //this.set(this.editor.getValue()),
      this.oldValue = this.editor.getValue();
    });
  },
  methods:{ editor(){ return this.editor } }
};

module.exports = {
  name: "editor-home",
  components: {
    'view-block-list': httpVueLoader('./viewBlocksList.vue'),
    'new-block-component': httpVueLoader('./newBlockComponent.vue'),
    'new-input-component': httpVueLoader('./newInputFieldComponent.vue'),
    'vue-ace-editor': VueAceEditor
  },
  data() {
    return {
      showBlocksList: false,
      fields: ['name', 'content', 'type' ,'controls'],
      items: [],
      blockData: [],
      showInput: false,
      showControls: false,
      inputFieldData: {},
      localAddress: window.location.protocol + "//" + window.location.host + '/vloxrenderer.html',
      renderDesktop: true,
      //Ace editor section
      editorcontent: '',

      //  options object
      //  https://github.com/ajaxorg/ace/wiki/Configuring-Ace
      htmlEdtOptions: {
        mode:'html',
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
      styleEdtOptions: {
        mode:'scss',
        theme: 'monokai',
        fontSize: 11,
        fontFamily: 'monospace',
        highlightActiveLine: false,
        highlightGutterLine: false,
        maxLines: 20,
        minLines: 20,
      },
      codeEdtOptions: {
        mode:'javascript',
        theme: 'monokai',
        fontSize: 11,
        fontFamily: 'monospace',
        highlightActiveLine: false,
        highlightGutterLine: false,
        maxLines: 20,
        minLines: 20,
      },
      beautifyConfig: {
        "indent_size": "2",
        "indent_char": " ",
        "max_preserve_newlines": "-1",
        "preserve_newlines": false,
        "keep_array_indentation": false,
        "break_chained_methods": false,
        "indent_scripts": "normal",
        "brace_style": "none",
        "space_before_conditional": false,
        "unescape_strings": false,
        "jslint_happy": false,
        "end_with_newline": false,
        "wrap_line_length": "0",
        "indent_inner_html": false,
        "comma_first": false,
        "e4x": false,
        "indent_empty_lines": false
      }
    }
  },
  methods: {
    setUpSizePreview(render) {
      this.renderDesktop = render;
    },
    onSelectBlock (blockData) {
      this.blockData = blockData;
      this.showControls = true;
      //we initializae the data table
      const blockContents =  blockData.properties.items instanceof Array ?
          blockData.properties.items:
          JSON.parse(blockData.properties).items;
      this.items = blockContents;//[];
      /*blockContents.forEach(element => {
        this.items.push({ variable_name: element.name, default_value: element.content, variable_type: element.type});
      });*/
      if (document.getElementById('componentPreview')) {
        document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
      }
    },
    addInputField() {
      this.items.push({content: "Input a value", name: "Input a name", type: "text"});
    },
    onRowSelected(items) {
      if (items !== undefined && items.length > 0) {
        this.inputFieldData = items[0];
        this.showInput = true;
      }
    },
    saveInput(){
      this.showInput = false;
    },
    cancelEdit() {
      this.showInput = false;
    },
    save() {
      let axiosConfig = {
        headers: {
          'Content-Type': 'application/json',
          'Access-Control-Allow-Origin': "*",
        }
      };
      const modalRef = this.$bvModal;
      // TODO reenable to use rigth api endpoint instead of this
      //  axios.post(window.location.protocol + "//" + window.location.host + '/modxMonster/rest/Resources/'
      axios.put(window.location.protocol + "//" + window.location.host +
                  Vue.prototype.$restRoute + '/rest/index.php?_rest=Blocks/'
          + this.blockData.id,
          this.blockData,
          axiosConfig)
          .then(response => {
            modalRef.hide('add-content');
            document.getElementById('componentPreview').src = document.getElementById('componentPreview').src;
          })
          .catch(error => {
            console.log(error);
            this.showErrorAjax();
          });
    },
    highlighter(code) {
      // js highlight example
      return Prism.highlight(code, Prism.languages.js, "js");
    }
  },
  updated() {
    if (document.getElementById("editor1") && !this.loaded) {
      this.loaded = true;
    }
  }
};
</script>
<style scope>
h3 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #636363;
  padding: .2rem;
}
.previewAreaWidth {
  max-width: 1320px;
  margin: 0 auto;

  -webkit-transition: all 0.5s ease-in-out;
  -moz-transition: all 0.5s ease-in-out;
  -o-transition: all 0.5s ease-in-out;
  -ms-transition: all 0.5s ease-in-out;
  transition: all 0.5s ease-in-out;
}
.previewDesktop {
  max-width: 1200px;
}
.previewMobile {
  max-width: 576px;
}
.previewBackground {
  background-color: #d4d4d4;
  padding: .25rem 0;
}
.previewArea {
  overflow: hidden;
  height: 260px;
  resize: both;
  border-top: 1px solid #c8c9ca;
  padding: 0;
  border-bottom: 1px solid #c8c9ca;
}
.previewExtraHeight {
  height: 400px;
  border: none;
  height: calc(100vh - 80px);
}
.firstScreen {
  text-align: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -60%);
}
.firstScreen img {
  height: 100%;
  max-height: 160px;
  display: block;
}
.modXMonsterLogo {
  position: absolute;
  bottom: 2rem;
  right: 3rem;
}
.modXMonsterLogo img {
  max-height: 70px;
  margin: 0 0 .5rem 0;
}
.modXMonsterLogo p {
  text-align: center;
  margin: 0;
  font-weight: 600;
  color: #848484;
  font-size: .9rem;
}
.my-editor {
  resize: both;
}
td {
  padding: .7rem .6rem .6rem !important;
  position: relative;
}
td p {
  margin-bottom: 0;
}
td .btn {
  padding: 2px 16px !important;
  margin: 0 6px;
}
td button.dropdown-toggle {
  margin: 3px;
}
td button i {
  font-size: .9rem;
}
.form-control {
  font-size: 1rem;
  padding: 5px 10px;
}
td .btn-group button {
  margin-top: 3px;
}
.table>:not(caption)>*>* {
  box-shadow: inset 0 0 0 0 var(--bs-table-accent-bg);
}
.table.b-table > tbody > .table-active, .table.b-table > tbody > .table-active > th, .table.b-table > tbody > .table-active > td {
  background-color: #eeeeee;
}
.addkrakenBlockBig {
  margin: 2rem auto 0;
  padding: 1.3rem 3rem;
  background: whitesmoke;
}
.tabs {
  width: 100%;
}
.updatePrev {
  position: absolute;
  right: 10px;
  top: -2px;
  z-index: 1;
}
.previewButtons {
  text-align: center;
  margin: .1rem auto .5rem;
  max-width: 1200px;
}
.previewButtons h5 {
  color: #737373;
  position: absolute;
  font-size: 1.2rem;
  font-weight: 500;
  margin: .15rem;
}
.previewButtons button {
  background: transparent;
  border: none;
  padding: 0;
  margin: 0 0 0 1rem;
  width: auto;
  color: #6c757d;
}
.previewButtons button:first-child {
  margin: 0;
}
.previewButtons span {
  font-size: .8rem;
  font-weight: normal;
}
.previewButtons .icon-active, .previewButtons .icon-active span {
  color: white;
  -webkit-transition: all 0.25s ease-in-out;
  -moz-transition: all 0.25s ease-in-out;
  -o-transition: all 0.25s ease-in-out;
  -ms-transition: all 0.25s ease-in-out;
  transition: all 0.25s ease-in-out;
}
</style>