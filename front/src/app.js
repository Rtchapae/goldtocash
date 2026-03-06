import { createSSRApp, h } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import { createRouter } from './router'

export function createApp(history) {
	const app = createSSRApp({ render: () => h(App) })
	const pinia = createPinia()
	const router = createRouter(history)

	app.use(pinia)
	app.use(router)

	return { app, router, pinia }
}


