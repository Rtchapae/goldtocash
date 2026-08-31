import { get, post, put, del, apiRequest } from './client'

export const fetchBuilderPages = async (params = {}) => {
	const queryParams = new URLSearchParams()
	Object.keys(params).forEach((key) => {
		if (params[key] !== null && params[key] !== undefined && params[key] !== '') {
			queryParams.append(key, params[key])
		}
	})
	const qs = queryParams.toString()
	return get(`/admin/builder-pages${qs ? `?${qs}` : ''}`)
}

export const getBuilderPage = async (id) => get(`/admin/builder-pages/${id}`)

export const createBuilderPage = async (payload) => post('/admin/builder-pages', payload)

export const updateBuilderPage = async (id, payload) => put(`/admin/builder-pages/${id}`, payload)

export const deleteBuilderPage = async (id) => del(`/admin/builder-pages/${id}`)

export const uploadBuilderPageImage = async (file) => {
	const formData = new FormData()
	formData.append('image', file)
	const response = await apiRequest('/admin/builder-pages/upload-image', {
		method: 'POST',
		body: formData,
	})
	return response.json()
}

export const importBuilderPageDocx = async (file) => {
	const formData = new FormData()
	formData.append('file', file)
	const response = await apiRequest('/admin/builder-pages/import-docx', {
		method: 'POST',
		body: formData,
	})
	return response.json()
}
