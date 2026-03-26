import {fileURLToPath, URL} from "node:url";

import {defineConfig} from "vite";
import vue from "@vitejs/plugin-vue";

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  build: {
    outDir: '../public/feed-list',
    assetsDir: '',
    manifest: 'manifest.json',
    rollupOptions: {
      input: "./src/main.ts",
      output: {
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]',
      }
    }
  },
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});
