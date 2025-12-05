import { defineConfig } from 'vite';

export default defineConfig({
  build: {
    outDir: 'dist',
    // Enable minification (esbuild is faster and built-in)
    minify: 'esbuild', // Options: 'esbuild' (default, faster) or 'terser' (requires installation)
    sourcemap: false, // Set to true for production debugging if needed
    cssCodeSplit: true, // Split CSS into separate files
    cssMinify: true, // Minify CSS
    rollupOptions: {
      input: {
        main: './js/main.js',
        modern: './js/modern-main.js',
        admin: './admin/js/admin.js',
        api: './js/api.js',
        validation: './js/validation.js',
        notifications: './js/notifications.js',
        utils: './js/utils.js',
        modals: './js/modals.js',
        'modal-crud': './js/modal-crud.js',
        'file-upload': './js/file-upload.js',
        tables: './js/tables.js',
        editor: './js/editor.js',
        charts: './js/charts.js',
        'error-tracking': './js/error-tracking.js',
        'animations-optimized': './js/animations-optimized.js',
        'image-preloader': './js/image-preloader.js',
        store: './js/store.js',
        'excel-export': './js/excel-export.js',
        'pdf-generator': './js/pdf-generator.js',
        'form-handler': './admin/js/form-handler.js',
        'drag-sort': './js/drag-sort.js',
        'modal-handlers': './js/modal-handlers.js',
        'lazy-load': './js/lazy-load.js',
        'scroll-animations': './js/scroll-animations.js',
        'schemas': './js/schemas.js',
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: 'assets/[name]-[hash].[ext]',
        // Optimized manual chunk splitting for better caching and smaller bundles
        manualChunks: (id) => {
          // Vendor chunks - split large libraries
          if (id.includes('node_modules')) {
            // Charts library
            if (id.includes('chart.js')) {
              return 'vendor-charts';
            }
            // Tables libraries
            if (id.includes('tabulator-tables')) {
              return 'vendor-tabulator';
            }
            if (id.includes('sortablejs')) {
              return 'vendor-sortable';
            }
            // Rich text editor
            if (id.includes('quill')) {
              return 'vendor-quill';
            }
            // File upload libraries
            if (id.includes('filepond')) {
              return 'vendor-filepond';
            }
            // HTTP client
            if (id.includes('axios')) {
              return 'vendor-axios';
            }
            // Animation libraries
            if (id.includes('gsap')) {
              return 'vendor-gsap';
            }
            if (id.includes('aos')) {
              return 'vendor-aos';
            }
            if (id.includes('locomotive-scroll')) {
              return 'vendor-locomotive';
            }
            // Date utilities
            if (id.includes('luxon') || id.includes('date-fns') || id.includes('dayjs')) {
              return 'vendor-date';
            }
            // Validation libraries
            if (id.includes('validator') || id.includes('yup')) {
              return 'vendor-validation';
            }
            // Lazy loading
            if (id.includes('vanilla-lazyload')) {
              return 'vendor-lazyload';
            }
            // Modal library
            if (id.includes('micromodal')) {
              return 'vendor-micromodal';
            }
            // Notifications
            if (id.includes('notyf')) {
              return 'vendor-notyf';
            }
            // PDF/Excel generation
            if (id.includes('jspdf') || id.includes('xlsx')) {
              return 'vendor-export';
            }
            // State management
            if (id.includes('zustand')) {
              return 'vendor-store';
            }
            // Error tracking
            if (id.includes('@sentry')) {
              return 'vendor-sentry';
            }
            // Lodash utilities
            if (id.includes('lodash')) {
              return 'vendor-lodash';
            }
            // Core vendor bundle (small, frequently used libraries)
            return 'vendor-core';
          }
        },
      },
    },
    // Increase chunk size warning limit temporarily (will decrease after optimization)
    chunkSizeWarningLimit: 800, // Reduced from 1000 to encourage optimization

  },
  server: {
    port: 3000,
    open: false,
    proxy: {
      '/api': {
        target: 'http://localhost',
        changeOrigin: true,
      },
    },
  },
  resolve: {
    alias: {
      '@': '/js',
    },
  },
});
