import { createApp } from './app'
import { createWebHistory } from 'vue-router'

const ssrIsMobile = typeof window !== 'undefined' && window.__SSR_MOBILE__ === true

const { app, router, pinia } = createApp(createWebHistory())
app.provide('ssrIsMobile', ssrIsMobile)

if (window.__PINIA__) {
	pinia.state.value = window.__PINIA__
}

router.isReady().then(() => {
	app.mount('#app', true)

	const initBootstrapWidgets = async () => {
		const bootstrap = await import('bootstrap')
		window.bootstrap = bootstrap

		const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		tooltipTriggerList.forEach((el) => {
			new bootstrap.Tooltip(el)
		})
		const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		popoverTriggerList.forEach((el) => {
			new bootstrap.Popover(el)
		})
	}

	const idle = window.requestIdleCallback || ((cb) => setTimeout(cb, 200))
	idle(() => { initBootstrapWidgets() })
})
