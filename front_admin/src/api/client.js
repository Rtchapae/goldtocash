
const FRONT_API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1'
const API_BASE_URL = import.meta.env.VITE_ADMIN_API_BASE_URL || FRONT_API_BASE_URL

const defaultOptions = {
	headers: {
		Accept: 'application/json',
		'Content-Type': 'application/json'
	}
}

const getAdminToken = () => {
	if (typeof window === 'undefined') {
		return null
	}

	return window.localStorage.getItem('admin_access_token')
}

export const apiRequest = async (endpoint, options = {}, skipAuth = false) => {
	const normalizedEndpoint = endpoint.startsWith('/') ? endpoint : `/${endpoint}`
	const url = `${API_BASE_URL}${normalizedEndpoint}`

	const isFormData = options.body instanceof FormData

	const config = {
		...defaultOptions,
		...options,
		headers: {
			'Accept': 'application/json',
			...(isFormData ? {} : { 'Content-Type': 'application/json' }),
			...(options.headers || {})
		}
	}

	if (!skipAuth) {
		const token = getAdminToken()
		if (token) {
			config.headers['Authorization'] = `Bearer ${token}`
		}
	}

	try {
		const response = await fetch(url, config)

		if (!response.ok) {
			const errorData = await response.json().catch(() => ({
				message: `HTTP error! status: ${response.status}`
			}))

			if (response.status === 401) {
				if (typeof window !== 'undefined') {
					window.localStorage.removeItem('admin_access_token')
					if (window.location.pathname !== '/login') {
						window.location.href = '/login'
					}
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
		console.error(`Admin API request failed: ${endpoint}`, error)
		}
		throw error
	}
}

export const get = async (endpoint, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'GET',
		...options
	})

	return response.json()
}

export const post = async (endpoint, data, options = {}, skipAuth = false) => {
	const response = await apiRequest(
		endpoint,
		{
			method: 'POST',
			body: JSON.stringify(data),
			...options
		},
		skipAuth
	)

	return response.json()
}

export const put = async (endpoint, data, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'PUT',
		body: JSON.stringify(data),
		...options
	})

	return response.json()
}

export const del = async (endpoint, options = {}) => {
	const response = await apiRequest(endpoint, {
		method: 'DELETE',
		...options
	})

	return response.json()
}

export { API_BASE_URL }

