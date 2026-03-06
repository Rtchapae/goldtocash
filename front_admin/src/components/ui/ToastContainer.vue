<template>
	<div
		aria-live="polite"
		aria-atomic="true"
		class="toast-container position-fixed top-0 end-0 p-3"
		style="z-index: 9999;"
	>
		<div
			v-for="toast in toasts"
			:key="toast.id"
			:class="['toast', 'show', `bg-${getToastColorClass(toast.type)}`]"
			role="alert"
			aria-live="assertive"
			aria-atomic="true"
		>
			<div class="toast-header">
				<iconify-icon
					:icon="getToastIcon(toast.type)"
					:class="['icon', `text-${getToastColorClass(toast.type)}`]"
				></iconify-icon>
				<strong class="me-auto ms-2">{{ toast.title || getToastTitle(toast.type) }}</strong>
				<button
					type="button"
					class="btn-close"
					aria-label="Close"
					@click="removeToast(toast.id)"
				></button>
			</div>
			<div class="toast-body text-white">
				{{ toast.message }}
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onUnmounted } from 'vue'

const toasts = ref([])
const timers = new Map()

const getToastIcon = (type) => {
	switch (type) {
		case 'error': return 'solar:close-circle-bold'
		case 'success': return 'solar:check-circle-bold'
		case 'warning': return 'solar:danger-triangle-bold'
		default: return 'solar:info-circle-bold'
	}
}

const getToastColorClass = (type) => {
	switch (type) {
		case 'error': return 'danger'
		case 'success': return 'success'
		case 'warning': return 'warning'
		default: return 'info'
	}
}

const getToastTitle = (type) => {
	switch (type) {
		case 'error': return 'Error'
		case 'success': return 'Success'
		case 'warning': return 'Warning'
		default: return 'Info'
	}
}

const removeToast = (id) => {
	const index = toasts.value.findIndex(t => t.id === id)
	if (index > -1) {
		toasts.value.splice(index, 1)
	}
	
	// Clear timer if exists
	if (timers.has(id)) {
		clearTimeout(timers.get(id))
		timers.delete(id)
	}
}

const addToast = (message, type = 'info', title = null, duration = 5000) => {
	const id = Date.now() + Math.random()
	const toast = {
		id,
		message,
		type,
		title,
	}
	
	toasts.value.push(toast)
	
	if (duration > 0) {
		const timer = setTimeout(() => {
			removeToast(id)
		}, duration)
		timers.set(id, timer)
	}
	
	return id
}

if (typeof window !== 'undefined') {
	window.__toast = {
		success: (message, title = null, duration = 5000) => addToast(message, 'success', title, duration),
		error: (message, title = null, duration = 7000) => addToast(message, 'error', title, duration),
		info: (message, title = null, duration = 5000) => addToast(message, 'info', title, duration),
		warning: (message, title = null, duration = 6000) => addToast(message, 'warning', title, duration),
	}
}

onUnmounted(() => {
	timers.forEach(timer => clearTimeout(timer))
	timers.clear()
})
</script>

<style scoped>
.toast-container {
	min-width: 300px;
	max-width: 400px;
}

.toast {
	opacity: 1;
	margin-bottom: 0.5rem;
}

.toast-header {
	background-color: rgba(255, 255, 255, 0.1);
	border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.toast-body {
	background-color: transparent;
}

.icon {
	font-size: 1.2rem;
}
</style>

