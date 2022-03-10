const path = require('path')

const isMainApp = process.env.APP_TYPE === 'app1'
console.log('asdf' + process.env.APP_TYPE);
const appDir = isMainApp ? 'app1' : 'app2'

module.exports = {
  outputDir: path.resolve(__dirname, `${appDir}/dist`),
  chainWebpack: config => {

    // I've ommited all the non-relevant config stuff

    config.resolve.alias.set('@I', path.resolve(__dirname, '../interfaces'))
    config.resolve.alias.set('@shared', path.resolve(__dirname, './shared'))

    config.plugin("html").tap(args => {
      args[0].template = path.resolve(__dirname, `${appDir}/index.html`)
      return args
    })

  },
  devServer: {
    port: isMainApp ? 8080 : 7070
  },
}