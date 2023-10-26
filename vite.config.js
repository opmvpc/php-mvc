// vite.config.js
import { defineConfig } from "vite";

export default defineConfig({
  publicDir: "false",
  build: {
    outDir: "public/assets",
    assetsDir: "",
    manifest: true,
    copyPublicDir: false,
    modulePreload: {
      polyfill: false,
    },
    rollupOptions: {
      input: "resources/app.js",
    },
  },
});
