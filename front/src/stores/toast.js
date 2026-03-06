import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
	const toasts = ref([])

	const add = (options) => {
		const id = `toast-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`
		const toast = {
			id,
			message: options.message || '',
			type: options.type || 'info',
			duration: options.duration !== undefined ? options.duration : 5000,
			timestamp: Date.now(),
		}

		toasts.value.push(toast)

		if (toast.duration > 0) {
			setTimeout(() => {
				remove(id)
			}, toast.duration)
		}

		return id
	}

	const remove = (id) => {
		const index = toasts.value.findIndex((t) => t.id === id)
		if (index > -1) {
			toasts.value.splice(index, 1)
		}
	}

	const clear = () => {
		toasts.value = []
	}

	const success = (message, duration = 5000) => {
		return add({ message, type: 'success', duration })
	}

	const error = (message, duration = 7000) => {
		return add({ message, type: 'error', duration })
	}

	const warning = (message, duration = 5000) => {
		return add({ message, type: 'warning', duration })
	}

	const info = (message, duration = 5000) => {
		return add({ message, type: 'info', duration })
	}

	return {
		toasts,
		add,
		remove,
		clear,
		success,
		error,
		warning,
		info,
	}
})


