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
  chainWebpack: config => {
    config.resolve.alias.set('@I', path.resolve(__dirname, '../interfaces'))
    config.resolve.alias.set('@shared', path.resolve(__dirname, './shared'))

    config.plugin("html").tap(args => {
      args[0].template = path.resolve(__dirname, `${appDir}/index.html`)
      return args
    })

  },
  devServer: {
    "port": 8080,
    "https": {
      "key": fs.readFileSync('./certs/ssl.key'),
      "cert": fs.readFileSync('./certs/ssl.crt')
    },
    proxy: {
      '^/assets/components/vlox': {
        target: 'https://localhost',
        changeOrigin: true
      },
      '^/assets/images': {
        target: 'https://localhost',
        changeOrigin: true
      },
    }
  }
}