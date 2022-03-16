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
    debugger;
    const response = await axios.get(window.location.protocol + "//" + window.location.host +
      Vue.prototype.$restRoute + '/rest/index.php?_rest=Ide/RENDERER',
      axiosConfig)
    return response.data;
  }
}
