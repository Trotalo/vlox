<!--
  - This file is part of VloX.
  -
  - Copyright (c) TROTALO, SAS. All Rights Reserved.
  -
  - For complete copyright and license information, see the COPYRIGHT and LICENSE
  - files found in the top-level directory of this distribution.
  -->

<template>
<!--  <div :id="id ? id: $options._componentTag +'-'+ _uid"
       :class="$options._componentTag">
    <slot></slot>
  </div>-->
  <div class="ace-container">
    <!-- ID is used in official documents, it is forbidden to use it here, it is easy to cause problems after packaging later, just use ref or DOM -->
    <div class="ace-editor" ref="ace"></div>
  </div>
</template>

<script>
import ace from'ace-builds'
import'ace-builds/webpack-resolver'//must be imported for use in the webpack environment
import'ace-builds/src-noconflict/theme-monokai'//theme set by default
import'ace-builds/src-noconflict/mode-javascript'//Language mode set by default

export default {
  name: "vueAceEditor",
  props:['value','id','options'],
  data () {
    return {
      editor: null,
      themePath:'ace/theme/monokai',//If webpack-resolver is not imported, the module path will report an error
      modePath:'ace/mode/javascript'//Same as above
    }
  },
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
    //this.editor = window.ace.edit(this.$el.id);
    debugger;
    this.editor = ace.edit(this.$refs.ace); /*, {
      maxLines: 20,//The maximum number of lines, scroll bars will appear automatically if exceeded
      minLines: 10,//The minimum number of lines, when the maximum number of lines is not reached, the editor will automatically expand and contract
      fontSize: 14,//Font size in the editor
      theme: this.themePath,//theme set by default
      mode: this.modePath,//Language mode set by default
      tabSize: 4//Tab is set to 4 spaces
    })*/

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
  //methods:{ editor(){ return this.editor } }
}
</script>

<style scoped>

</style>