import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import fs from 'node:fs';
import path from 'node:path';

function copyTinyMcePlugin() {
	const copy = () => {
		const src = path.resolve(__dirname, 'node_modules/tinymce');
		const dest = path.resolve(__dirname, 'public/tinymce');
		if (!fs.existsSync(src)) return;
		fs.mkdirSync(dest, { recursive: true });
		fs.cpSync(src, dest, { recursive: true });
	};
	return {
		name: 'copy-tinymce',
		buildStart: copy,
		configureServer: copy,
	};
}

export default defineConfig({
	plugins: [
		copyTinyMcePlugin(),
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
