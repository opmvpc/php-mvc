// vite.config.js
import { defineConfig, configureServer } from "vite";

export default defineConfig({
  plugins: [CustomHmr()],
  publicDir: "false",
  server: {
    watch: {
      paths: ["resources/views/**/*.php", "app/**/*.php"],
      depth: 99,
      usePolling: true,
    },
  },
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

function CustomHmr() {
  // from https://stackoverflow.com/a/69628635
  return {
    name: "custom-hmr",
    enforce: "post",
    handleHotUpdate({ file, server }) {
      if (file.endsWith(".php") && file.includes("/tests/") === false) {
        console.log("reloading php file...");

        server.ws.send({
          type: "full-reload",
          path: "*",
        });
      }
    },
  };
}
