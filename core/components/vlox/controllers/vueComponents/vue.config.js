/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */
var fs = require('fs');
const webpack = require('webpack')
const path = require('path')
const appDir = process.env.APP_TYPE;

module.exports = {
  outputDir: path.resolve(__dirname, `${appDir}/dist`),
  assetsDir: `vlox/assets/components/vlox/${appDir}`,
  configureWebpack: {
    plugins: [
      new webpack.NormalModuleReplacementPlugin(/^file-loader\?esModule=false!(.*)/, (res) => {
        res.request = res.request.replace(/^file-loader\?esModule=false!/, `file-loader?esModule=false&outputPath=vlox/assets/components/vlox/ace-editor-modes!`)
      }),
    ],
  },
  chainWebpack: config => {
    config.resolve.alias.set('@I', path.resolve(__dirname, '../interfaces'))
    config.resolve.alias.set('@shared', path.resolve(__dirname, './shared'))
    config.plugin("html").tap(args => {
      args[0].template = path.resolve(__dirname, `${appDir}/index.html`)
      return args
    })
  },
  devServer: {
    "port": 9090,
    "https": {
      "key": fs.readFileSync('../../vue-res/certs/ssl.key'),
      "cert": fs.readFileSync('../../vue-res/certs/ssl.crt')
    },
    proxy: {
      '^/vlox': {
        target: 'https://172.25.114.131',
        changeOrigin: true
      },
    }
  }
}