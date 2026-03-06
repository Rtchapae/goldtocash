import { get, post, put, del } from './client'

export const fetchAdminUsers = async (params = {}) => {
	const queryParams = new URLSearchParams()
	
	if (params.per_page !== undefined && params.per_page !== null) {
		queryParams.set('per_page', String(params.per_page))
	}
	
	if (params.page !== undefined && params.page !== null) {
		queryParams.set('page', String(params.page))
	}
	
	if (params.search) {
		queryParams.set('search', params.search)
	}
	
	if (params.role && params.role !== 'all') {
		queryParams.set('role', params.role)
	}

	const queryString = queryParams.toString()
	return get(`/admin/admin-users${queryString ? '?' + queryString : ''}`)
}

export const createAdminUser = async (data) => {
	return post('/admin/admin-users', data)
}

export const updateAdminUser = async (userId, data) => {
	return put(`/admin/admin-users/${userId}`, data)
}

export const deleteAdminUser = async (userId) => {
	return del(`/admin/admin-users/${userId}`)
}

