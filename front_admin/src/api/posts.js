import { get, del, post } from './client'
import { apiRequest } from './client'

export const fetchPosts = async (params = {}) => {
	const queryParams = new URLSearchParams()
	
	Object.keys(params).forEach(key => {
		if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
			queryParams.append(key, params[key])
		}
	})

	const queryString = queryParams.toString()
	const endpoint = `/admin/posts${queryString ? `?${queryString}` : ''}`
	
	return await get(endpoint)
}

export const createPost = async (formData) => {
	const response = await apiRequest('/admin/posts', {
		method: 'POST',
		body: formData
	})

	if (!response.ok) {
		const errorData = await response.json().catch(() => ({
			message: `HTTP error! status: ${response.status}`
		}))
		
		let errorMessage = errorData.message || errorData.error
		if (response.status === 422 && errorData.errors) {
			const validationErrors = Object.values(errorData.errors).flat()
			errorMessage = validationErrors.join(', ') || 'Validation failed'
		}
		
		const error = new Error(errorMessage || `HTTP error! status: ${response.status}`)
		error.status = response.status
		error.data = errorData
		throw error
	}

	return response.json()
}

export const getPost = async (id) => {
	return await get(`/admin/posts/${id}`)
}

export const updatePost = async (id, formData) => {
	const response = await apiRequest(`/admin/posts/${id}`, {
		method: 'POST',
		body: formData
	})

	if (!response.ok) {
		const errorData = await response.json().catch(() => ({
			message: `HTTP error! status: ${response.status}`
		}))
		
		let errorMessage = errorData.message || errorData.error
		if (response.status === 422 && errorData.errors) {
			const validationErrors = Object.values(errorData.errors).flat()
			errorMessage = validationErrors.join(', ') || 'Validation failed'
		}
		
		const error = new Error(errorMessage || `HTTP error! status: ${response.status}`)
		error.status = response.status
		error.data = errorData
		throw error
	}

	return response.json()
}

export const deletePost = async (id) => {
	return await del(`/admin/posts/${id}`)
}

