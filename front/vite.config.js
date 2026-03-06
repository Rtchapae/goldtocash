import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'

export default defineConfig({
	plugins: [vue()],
	resolve: {
		alias: [
			// Force ESM build for SSR. Important: only match bare "vue" so "vue/server-renderer" still works.
			{ find: /^vue$/, replacement: 'vue/dist/vue.runtime.esm-bundler.js' },
			{ find: '@', replacement: path.resolve(__dirname, 'src') },
			{ find: '~styles', replacement: path.resolve(__dirname, 'src/styles') },
			{ find: '~assets', replacement: path.resolve(__dirname, 'src/assets') },
		],
	},
	define: {
		__SSR__: process.env.VITE_SSR === 'true'
	},
	css: {
		preprocessorOptions: {
			scss: {}
		}
	},
	server: {
		host: '0.0.0.0',
		port: 5175,
		strictPort: true,
		hmr: {
			server: false, // Полностью отключаем HMR сервер
			port: false
		},
		watcher: null, // Отключаем file watcher
		// Отключаем websocket сервер полностью
		ws: false,
		fs: {
			allow: [
				__dirname,
				path.resolve(__dirname, '../html')
			]
		}
	},
	ssr: {
		noExternal: ['vue', 'vue-router', 'pinia', 'vue-demi', '@vue/devtools-api']
	},
	optimizeDeps: {
		include: ['vue', 'vue-router', 'pinia', 'bootstrap']
	},
	build: {
		outDir: 'dist/client',
		emptyOutDir: false
	}
})


