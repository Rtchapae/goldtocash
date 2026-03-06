import { get, post, put, del } from './client'

export const fetchSeoPages = async (params = {}) => {
	const queryParams = new URLSearchParams()

	const perPageValue = params.per_page !== undefined ? params.per_page : params.perPage
	if (perPageValue !== undefined && perPageValue !== null) {
		queryParams.set('per_page', String(perPageValue))
	}

	if (params.page !== undefined && params.page !== null) {
		queryParams.set('page', String(params.page))
	}
	if (params.search) {
		queryParams.set('search', params.search)
	}

	const queryString = queryParams.toString()
	return get(`/admin/seo-pages${queryString ? '?' + queryString : ''}`)
}

export const getSeoPage = async (id) => {
	return get(`/admin/seo-pages/${id}`)
}

export const createSeoPage = async (data) => {
	return post('/admin/seo-pages', data)
}

export const updateSeoPage = async (id, data) => {
	return put(`/admin/seo-pages/${id}`, data)
}

export const deleteSeoPage = async (id) => {
	return del(`/admin/seo-pages/${id}`)
}
