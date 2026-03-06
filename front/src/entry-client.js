import { createApp } from './app'
import { createWebHistory } from 'vue-router'

// Import Bootstrap JS
import * as bootstrap from 'bootstrap'

// Make Bootstrap available globally for components that need it
window.bootstrap = bootstrap

const { app, router, pinia } = createApp(createWebHistory())

if (window.__PINIA__) {
	pinia.state.value = window.__PINIA__
}

// Wait for router to be ready and add 100ms delay to prevent image flickering
router.isReady().then(() => {
	app.mount('#app', true)
	
	// Initialize Bootstrap tooltips and popovers after mount
	// Bootstrap 5 doesn't auto-initialize, so we need to do it manually
	if (typeof window !== 'undefined') {
		// Initialize tooltips
		const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		tooltipTriggerList.map(function (tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
		
		// Initialize popovers
		const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
		popoverTriggerList.map(function (popoverTriggerEl) {
			return new bootstrap.Popover(popoverTriggerEl)
		})
	}
})


