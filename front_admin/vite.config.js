import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'node:path';

export default defineConfig({
	plugins: [
		vue({
			template: {
				compilerOptions: {
					// говорим Vue, что <iconify-icon> — нативный custom element, а не Vue‑компонент
					isCustomElement: (tag) => tag === 'iconify-icon'
				}
			}
		})
	],
	resolve: {
		alias: {
			'@': path.resolve(__dirname, 'src')
		}
	},
	server: {
		host: true,
		port: 5173,
		allowedHosts: ['m.goldtocash.us']
	},
	preview: {
		host: true,
		port: 5173,
		allowedHosts: ['m.goldtocash.us']
	}
});

