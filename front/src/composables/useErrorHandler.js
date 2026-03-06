export function useErrorHandler() {
	const handleApiError = (error, context = '', showToast = true) => {
		if (import.meta.env.DEV) {
			console.error(`${context} error:`, error)
		}

		let message = 'An unexpected error occurred. Please try again.'

		if (error?.status === 401) {
			message = 'Please log in to continue.'
		} else if (error?.status === 403) {
			message = 'You do not have permission to perform this action.'
		} else if (error?.status === 404) {
			message = 'The requested resource was not found.'
		} else if (error?.status === 422) {
			message = 'Please check your input and try again.'
		} else if (error?.status === 429) {
			message = 'Too many requests. Please wait a moment and try again.'
		} else if (error?.status === 500) {
			message = 'Server error. Please try again later.'
		} else if (error?.message) {
			message = error.message
		} else if (error?.data?.message) {
			message = error.data.message
		}

		if (showToast && typeof window !== 'undefined') {
			try {
				import('@/composables/useToast').then(({ useToast }) => {
					const toast = useToast()
					toast.error(message)
				})
			} catch (e) {
			}
		}

		return message
	}

	const handleFormError = (error, setErrorMessage = null) => {
		const message = handleApiError(error, 'Form submission', false)

		if (setErrorMessage && typeof setErrorMessage === 'function') {
			setErrorMessage(message)
		}

		return message
	}

	const handleDataLoadError = (error, dataType = 'data') => {
		handleApiError(error, `Loading ${dataType}`, false)

		switch (dataType) {
			case 'countries':
				return [
					{ code: 'USA', name: 'United States' },
					{ code: 'CA', name: 'Canada' },
					{ code: 'MX', name: 'Mexico' },
				]
			case 'calculator':
				return { price: 0 }
			case 'seo':
				return {
					title: 'Gold To Cash',
					description: 'Professional precious metals dealer',
					keywords: ['gold', 'silver']
				}
			default:
				return null
		}
	}

	return {
		handleApiError,
		handleFormError,
		handleDataLoadError
	}
}

