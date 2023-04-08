import { fileURLToPath, URL } from 'node:url'
import { resolve, dirname } from "node:path";

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import fs from 'fs';

import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

import VueI18nPlugin from "@intlify/unplugin-vue-i18n/vite";

// https://vitejs.dev/config/
export default defineConfig({
  root: '[[+project]]',
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: '[[+project]]/src/quasar-variables.sass'
    }),
    VueI18nPlugin({
      include: resolve(dirname(fileURLToPath(import.meta.url)), './locales/**'),
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./[[+project]]/src', import.meta.url))
      //'@': path.resolve(__dirname, '/[[+project]]/src'),
    }
  },
  server: {
    host: '0.0.0.0',
    https: {
      key:  fs.readFileSync("./certs/ssl.key"),
      cert: fs.readFileSync("./certs/ssl.crt"),
      //ca: fs.readFileSync("../.local/share/mkcert/rootCA.pem")

    },
    port: '5173',
    proxy: {
      '^/cronos': {
        target: 'https://192.168.0.103',
        changeOrigin: true,
        secure: false,
      },
      '^/assets': {
        target: 'https://192.168.0.103',
        changeOrigin: true,
        secure: false
      },
    }
  },
  build: {
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          let extType = assetInfo.name.split('.').at(1);
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
            extType = 'img';
          }
          //return `assets/[[+project]]/${extType}/[name]-[hash][extname]`;
          return `[[!getAssetsLocation? &project=`[[+project]]` &build=`[[+build]]`]]/${extType}/[name]-[hash][extname]`;
        },
        //chunkFileNames: 'assets/[[+project]]/js/[name]-[hash].js',
        //entryFileNames: 'assets/[[+project]]/js/[name]-[hash].js',
        chunkFileNames: '[[!getAssetsLocation? &project=`[[+project]]` &build=`[[+build]]`]]/js/[name]-[hash].js',
        entryFileNames: '[[!getAssetsLocation? &project=`[[+project]]` &build=`[[+build]]`]]/js/[name]-[hash].js',
      },
    },
  },
})
