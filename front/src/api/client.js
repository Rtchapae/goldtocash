const API_BASE_URL = import.meta.env.VITE_API_BASE_URL

const defaultOptions = {
	headers: {
		'Accept': 'application/json',
		'Content-Type': 'application/json',
	},
}

export const apiRequest = async (endpoint, options = {}, skipAuth = false) => {
	const normalizedEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`
	const url = `${API_BASE_URL}${normalizedEndpoint}`
	const isFormData = options.body instanceof FormData

	const config = {
		...defaultOptions,
		...options,
		headers: {
			...(isFormData ? {} : defaultOptions.headers),
			...(options.headers || {}),
		},
	}

	const isPublicEndpoint = endpoint.includes('/seo') || endpoint.includes('/calculator')
	if (!skipAuth && !isPublicEndpoint) {
		if (typeof window !== 'undefined') {
			const token = localStorage.getItem('jwt_token')
			if (token) {
				config.headers['Authorization'] = `Bearer ${token}`
			}
		}
	}

	try {
		const response = await fetch(url, config)

		if (!response.ok) {
			const errorData = await response.json().catch(() => ({ 
				message: `HTTP error! status: ${response.status}` 
			}))
			
			if (response.status === 401 && !isPublicEndpoint) {
				if (typeof window !== 'undefined') {
					localStorage.removeItem('jwt_token')
				}
			}
			
			const error = new Error(errorData.message || errorData.error || `HTTP error! status: ${response.status}`)
			error.status = response.status
			error.data = errorData
			throw error
		}

		return response
	} catch (error) {
		if (import.meta.env.DEV) {
		console.error(`API request failed: ${endpoint}`, error)
		}
		throw error
	}
}

export const get = async (endpoint, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'GET',
		...options,
	})
	return response.json()
}

export const post = async (endpoint, data, options = {}, skipAuth = false) => {
	const response = await apiRequest(endpoint, {
		method: 'POST',
		body: JSON.stringify(data),
		...options,
	}, skipAuth)
	return response.json()
}

export const put = async (endpoint, data, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'PUT',
		body: JSON.stringify(data),
		...options,
	})
	return response.json()
}

export const del = async (endpoint, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'DELETE',
		...options,
	})
	return response.json()
}

export const postFormData = async (endpoint, formData, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'POST',
		body: formData,
		headers: {
			'Accept': 'application/json',
		},
		...options,
	})
	return response.json()
}

export async function downloadFile(endpoint, options = {}, skipAuth = false) {
	return apiRequest(endpoint, {
		method: 'GET',
		...options,
	}, skipAuth)
}

