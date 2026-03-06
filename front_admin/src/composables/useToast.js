export const useToast = () => {
	const showToast = (message, type = 'info', title = null, duration = 5000) => {
		if (typeof window === 'undefined' || !window.__toast) {
			console[type === 'error' ? 'error' : type === 'success' ? 'log' : 'info'](message)
			return
		}
		
		return window.__toast[type](message, title, duration)
	}
	
	return {
		success: (message, title = null, duration = 5000) => showToast(message, 'success', title, duration),
		error: (message, title = null, duration = 7000) => showToast(message, 'error', title, duration),
		info: (message, title = null, duration = 5000) => showToast(message, 'info', title, duration),
		warning: (message, title = null, duration = 6000) => showToast(message, 'warning', title, duration),
	}
}

