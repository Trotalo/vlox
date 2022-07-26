/*
 * This file is part of VloX.
 *
 * Copyright (c) TROTALO, SAS. All Rights Reserved.
 *
 * For complete copyright and license information, see the COPYRIGHT and LICENSE
 * files found in the top-level directory of this distribution.
 */
var fs = require('fs');

const path = require('path')

/*const isMainApp = process.env.APP_TYPE === 'app1'
console.log('asdf' + process.env.APP_TYPE);*/
const appDir = process.env.APP_TYPE;

module.exports = {
  outputDir: path.resolve(__dirname, `${appDir}/dist`),
  assetsDir: `assets/${appDir}`,
  //publicPath: path.resolve(__dirname, `${appDir}/dist`),
  chainWebpack: config => {
    config.resolve.alias.set('@I', path.resolve(__dirname, '../interfaces'))
    config.resolve.alias.set('@shared', path.resolve(__dirname, './shared'))

    config.plugin("html").tap(args => {
      args[0].template = path.resolve(__dirname, `${appDir}/index.html`)
      return args
    })

  },
  pwa: {
    name: 'Vue Argon Design',
    themeColor: '#172b4d',
    msTileColor: '#172b4d',
    appleMobileWebAppCapable: 'yes',
    appleMobileWebAppStatusBarStyle: '#172b4d'
  },
  devServer: {
    port: 8080,
    https: true,
    host: '0.0.0.0',
    proxy: {
      '^/vlox/assets/components/vlox': {
        target: 'https://172.18.122.86',
        changeOrigin: true
      },
      '^/vlox/assets/images': {
        target: 'https://172.18.122.86',
        changeOrigin: true
      },
      '^/assets/images': {
        target: 'https://172.18.122.86',
        changeOrigin: true
      },
    }
  }
}