// nuxt.config.ts

export default defineNuxtConfig({
    compatibilityDate: '2026-02-14',
    devtools: { enabled: true },

    modules: [
        // '@nuxtjs/tailwindcss', // Temporary disabled due to compatibility issues
    ],

    // Remove postcss configuration from here since we have postcss.config.js

    components: {
        global: true,
        dirs: ['~/components'],
    },

    runtimeConfig: {
        public: {
            apiBase: process.env.API_BASE_URL || 'http://localhost:8000/api',
        },
    },

    build: {
        transpile: ['vee-validate'],
    },

    imports: {
        autoImport: true,
    },

    typescript: {
        strict: true,
        typeCheck: true,
    },
})
