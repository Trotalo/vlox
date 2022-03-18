/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */
import axios from "axios";
import './globalConstants';
import Vue from "vue";

const axiosConfig = {
  headers: {
    'Content-Type': 'application/json',
    'Access-Control-Allow-Origin': "*",
  }
}

/* eslint-disable no-unused-vars */
export default class Services {

  constructor() {
  }

  async getRendererId() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/RENDERER',
      axiosConfig)
    return response.data;
  }

  async newBlock(data) {
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Blocks/',
      data,
      axiosConfig);
    return response;
      /*.then(response => {
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
      });*/
  }
}

