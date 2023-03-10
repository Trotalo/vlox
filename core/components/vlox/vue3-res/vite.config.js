import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import fs from 'fs';

import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

// https://vitejs.dev/config/
export default defineConfig({
  root: '20',
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: '20/src/quasar-variables.sass'
    })
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./20/src', import.meta.url))
      //'@': path.resolve(__dirname, '/20/src'),
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
        target: 'https://172.17.3.95',
        changeOrigin: true
      },
      '^/assets': {
        target: 'https://172.17.3.95',
        changeOrigin: true
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
          return `assets/20/${extType}/[name]-[hash][extname]`;
        },
        chunkFileNames: 'assets/20/js/[name]-[hash].js',
        entryFileNames: 'assets/20/js/[name]-[hash].js',
      },
    },
  },
})
