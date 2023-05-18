import { fileURLToPath, URL } from 'node:url'
import { resolve, dirname } from "node:path";

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import fs from 'fs';

import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

import VueI18nPlugin from "@intlify/unplugin-vue-i18n/vite";

// https://vitejs.dev/config/
export default defineConfig({
  root: '23',
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: '23/src/quasar-variables.sass'
    }),
    VueI18nPlugin({
      include: resolve(dirname(fileURLToPath(import.meta.url)), './locales/**'),
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./23/src', import.meta.url))
      //'@': path.resolve(__dirname, '/23/src'),
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
          //return `assets/23/${extType}/[name]-[hash][extname]`;
          return `assets/components/cronos/23/${extType}/[name]-[hash][extname]`;
        },
        //chunkFileNames: 'assets/23/js/[name]-[hash].js',
        //entryFileNames: 'assets/23/js/[name]-[hash].js',
        chunkFileNames: 'assets/components/cronos/23/js/[name]-[hash].js',
        entryFileNames: 'assets/components/cronos/23/js/[name]-[hash].js',
      },
    },
  },
})
