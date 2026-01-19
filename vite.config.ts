import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig(({ mode }) => {
    // Mode options:
    // - 'unified' (default): Single bundle with all frameworks
    // - 'vue': Vue-only bundle (legacy)
    // - 'react': React-only bundle (legacy)
    // - 'vanilla': Vanilla-only bundle (legacy)
    const isUnified = mode === 'unified' || mode === 'production';
    const isVue = mode === 'vue';
    const isReact = mode === 'react';
    const isVanilla = mode === 'vanilla';

    // Determine entry file and output name
    let entry: string;
    let fileName: string;

    if (isUnified) {
        // Unified build - includes all frameworks
        entry = resolve(__dirname, 'resources/js/index.ts');
        fileName = 'accelade';
    } else if (isVue) {
        entry = resolve(__dirname, 'resources/js/vue/accelade.ts');
        fileName = 'accelade-vue';
    } else if (isReact) {
        entry = resolve(__dirname, 'resources/js/react/accelade.tsx');
        fileName = 'accelade-react';
    } else {
        entry = resolve(__dirname, 'resources/js/vanilla/accelade.ts');
        fileName = 'accelade-vanilla';
    }

    // Plugins - always include Vue and React for unified build
    const plugins = [];
    if (isUnified || isVue) {
        plugins.push(vue());
    }
    if (isUnified || isReact) {
        plugins.push(react());
    }

    return {
        plugins,

        build: {
            lib: {
                entry,
                name: 'Accelade',
                fileName,
                formats: ['iife', 'es'] as const,
            },
            outDir: 'dist',
            emptyOutDir: isUnified, // Only empty for unified build
            rollupOptions: {
                // Bundle everything - no externals for standalone usage
                external: [],
                output: [
                    {
                        format: 'iife' as const,
                        entryFileNames: `${fileName}.js`,
                        assetFileNames: '[name].[ext]',
                        inlineDynamicImports: true,
                        name: 'AcceladeModule',
                        exports: 'named' as const,
                    },
                    {
                        format: 'es' as const,
                        entryFileNames: `${fileName}.esm.js`,
                        assetFileNames: '[name].[ext]',
                        inlineDynamicImports: true,
                        exports: 'named' as const,
                    },
                ],
            },
            sourcemap: true,
            minify: 'terser' as const,
            terserOptions: {
                compress: {
                    drop_console: false,
                },
            },
            // Ensure ES6 class semantics are preserved for @event-calendar plugins
            target: 'es2020',
        },

        // esbuild options to preserve ES6 class semantics
        esbuild: {
            target: 'es2020',
            keepNames: true,
        },

        optimizeDeps: {
            esbuildOptions: {
                target: 'es2020',
            },
        },

        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/js'),
                // Use Vue's full build with runtime compiler for template support
                'vue': 'vue/dist/vue.esm-bundler.js',
                // Use pre-compiled dist files for @event-calendar v5 (all plugins bundled in core)
                '@event-calendar/core/index.css': resolve(__dirname, 'node_modules/@event-calendar/core/dist/index.css'),
                '@event-calendar/core': resolve(__dirname, 'node_modules/@event-calendar/core/dist/index.js'),
            },
        },

        define: {
            'process.env.NODE_ENV': JSON.stringify('production'),
            'process.env': JSON.stringify({}),
            // Feature flags for Vue
            '__VUE_OPTIONS_API__': JSON.stringify(true),
            '__VUE_PROD_DEVTOOLS__': JSON.stringify(false),
            '__VUE_PROD_HYDRATION_MISMATCH_DETAILS__': JSON.stringify(false),
        },
    };
});
