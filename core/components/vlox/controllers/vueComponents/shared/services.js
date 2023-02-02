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
    const response =  await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resId,
      {
        'oper': 'NPM_MODULE',
        'module': npmModule,
        'action': action
      },
      axiosConfig);
    return response;
  }

  static async saveBlockData(blockData){
    const response =  await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Blocks/'
      + blockData.id,
      blockData,
      axiosConfig);
    return response;
  }

  static async saveResData(blockData){
    const response =  await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Resources/'
      + this.blockData.id,
      blockData,
      axiosConfig);
    return response;
  }

  static async getBlockData(blockId){
    return await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute +
      '/rest/index.php?_rest=blocks/' + blockId, axiosConfig);
  }

  static async updateIde(resourceId, isEditingVlox){
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resourceId,
      {'oper': 'UPDATE',
            'isEditingVlox': isEditingVlox},
      axiosConfig);
    return response;
  }

  static async buildResource(resId){
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resId,
      {
        'oper': 'BUILD'
      },
      axiosConfig);
    return response;
  }

  static async startServer(resourceId, isEditingVlox) {
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/'
      + resourceId,
      {'oper': 'RUN',
            'isEditingVlox': isEditingVlox},
      axiosConfig);
    return response;
  }

  static async stopServer(){
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/',
      {'oper': 'STOP'},
      axiosConfig);
    return response;
  }

  static async isNpmInstalled() {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/NPM_INSTALLED',
      axiosConfig)
    return response.data;
  }

  static async installNpm() {
    const response = await axios.put(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide',
      {'oper': 'NPM_INSTALLED'},
      axiosConfig)
    return response.data;
  }

  static async isVloxOnRes(vloxId, resId) {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Resources/' + resId + '&vloxId=' + vloxId,
      axiosConfig);
    return (response.data && response.data.results && response.data.results.length > 0);
  }

  static async downloadCompiledResource(resLocation) {
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      "/" + resLocation,
      {
        responseType: 'blob',
        'Content-Type': 'blob',
        'Access-Control-Allow-Origin': "*",
      });
    var fileURL = window.URL.createObjectURL(new Blob([response.data]));
    var fileLink = document.createElement('a');
    fileLink.href = fileURL;
    const fileName = resLocation.substr(resLocation.lastIndexOf('/') + 1);
    fileLink.setAttribute('download', fileName);
    document.body.appendChild(fileLink);
    fileLink.click();
    return true;
  }

}

//export default new Services();