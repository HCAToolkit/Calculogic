import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig(({ mode }) => ({
  plugins: [react()],
  root: 'frontend',
  base: mode === 'development'
    ? 'http://localhost:5173/'      // dev server URL
    : '/wp-content/plugins/calculogic/assets/dist/',
  build: {
    outDir: path.resolve(__dirname, '../assets/dist'),
    emptyOutDir: true,
    manifest: true,
    rollupOptions: {
      input: path.resolve(__dirname, 'frontend/index.html'),
    },
  },
  server: {
    port: 5173,
    strictPort: true,
    cors: true,
  },
}));
