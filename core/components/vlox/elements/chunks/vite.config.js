import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

import { quasar, transformAssetUrls } from '@quasar/vite-plugin'

// https://vitejs.dev/config/
export default defineConfig({
  root: '[[+project]]',
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      sassVariables: '[[+project]]/src/quasar-variables.sass'
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
    port: '5173'

  }
})
