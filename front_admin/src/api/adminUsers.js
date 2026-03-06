import { get } from './client'

export const fetchAdminUsers = async (params = {}) => {
	const searchParams = new URLSearchParams()

	if (params.page) {
		searchParams.set('page', params.page)
	}

	if (params.per_page) {
		searchParams.set('per_page', params.per_page)
	}

	if (params.nameQuery) {
		searchParams.set('nameQuery', params.nameQuery)
	}

	const query = searchParams.toString()
	const endpoint = `/admin/users${query ? `?${query}` : ''}`

	return get(endpoint)
}

export const getAdminUserById = async (id) => {
	return get(`/admin/users/${id}`)
}

