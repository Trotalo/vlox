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

  /*constructor() {
  }*/

  static async getRendererId() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/RENDERER',
      axiosConfig)
    return response.data;
  }

  static async getNpmModules() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/NPM',
      axiosConfig)
    return response.data;
  }

  static async getMainJs() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/MAIN_JS',
      axiosConfig)
    return response.data;
  }

  static async saveMainJs(mainJs) {
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide',
      {'oper': 'SAVE_MAIN_JS',
              'contents': mainJs},
      axiosConfig)
    return response;
  }

  static async getNpmLog() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/NPM_LOG',
      axiosConfig)
    return response.data;
  }

  static async getNpmStatus(resId) {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/NPM_STATUS?resId=' + resId,
      axiosConfig)
    return response.data;
  }

  static async modifyNpmModule(npmModule, resId, action){
    return await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resId,
      {
        'oper': 'NPM_MODULE',
        'module': npmModule,
        'action': action
      },
      axiosConfig);
  }

  static async saveBlockData(blockData){
    return await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Blocks/'
      + blockData.id,
      blockData,
      axiosConfig);
  }

  static async getBlockData(blockId){
    return await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute +
      '/rest/index.php?_rest=blocks/' + blockId, axiosConfig);
  }

  static async updateIde(resourceId){
    return await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resourceId,
      {'oper': 'UPDATE'},
      axiosConfig);
  }

  static async buildResource(resId){
    return await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resId,
      {
        'oper': 'BUILD'
      },
      axiosConfig);
  }

}

//export default new Services();