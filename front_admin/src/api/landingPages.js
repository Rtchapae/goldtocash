import { get, del, post, put } from './client'
import { apiRequest } from './client'

export const fetchLandingPages = async (params = {}) => {
	const queryParams = new URLSearchParams()
	Object.keys(params).forEach((key) => {
		if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
			queryParams.append(key, params[key])
		}
	})
	const queryString = queryParams.toString()
	return await get(`/admin/landing-pages${queryString ? `?${queryString}` : ''}`)
}

export const getLandingPage = async (id) => {
	return await get(`/admin/landing-pages/${id}`)
}

export const createLandingPage = async (payload) => {
	return await post('/admin/landing-pages', payload)
}

export const updateLandingPage = async (id, payload) => {
	return await put(`/admin/landing-pages/${id}`, payload)
}

export const deleteLandingPage = async (id) => {
	return await del(`/admin/landing-pages/${id}`)
}

export const uploadLandingPageImage = async (file) => {
	const formData = new FormData()
	formData.append('file', file)
	const response = await apiRequest('/admin/landing-pages/upload-image', {
		method: 'POST',
		body: formData,
	})
	if (!response.ok) {
		const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }))
		const error = new Error(errorData.message || `HTTP error! status: ${response.status}`)
		error.status = response.status
		throw error
	}
	return response.json()
}
