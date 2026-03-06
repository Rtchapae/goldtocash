import { get, post, put, apiRequest } from './client'
import { buildOrdersQueryParams } from '@/utils/ordersQuery'

export const fetchPaidOrders = async (params = {}) => {
	const queryParams = buildOrdersQueryParams(params)

	const queryString = queryParams.toString()
	return get(`/admin/orders/paid${queryString ? '?' + queryString : ''}`)
}

export const fetchPendingOffers = async (params = {}) => {
	const queryParams = buildOrdersQueryParams(params)

	const queryString = queryParams.toString()
	return get(`/admin/orders/pending${queryString ? '?' + queryString : ''}`)
}

export const fetchAdminOrders = async (params = {}) => {
	const queryParams = buildOrdersQueryParams(params, {
		includeOrderType: true,
		includeBranchId: true
	})

	const queryString = queryParams.toString()
	return get(`/admin/orders${queryString ? '?' + queryString : ''}`)
}

export const getOrderDetails = async (orderId) => {
	return get(`/admin/orders/${orderId}`)
}

export const getOrderHistory = async (orderId) => {
	return get(`/admin/orders/${orderId}/history`)
}

export const getOrderFiles = async (orderId) => {
	return get(`/admin/orders/${orderId}/files`)
}

export const downloadOrderPdf = async (orderId) => {
	const response = await apiRequest(`/admin/orders/${orderId}/pdf`, {
		method: 'GET',
		headers: {
			Accept: 'application/pdf',
			'X-Requested-With': 'XMLHttpRequest'
		}
	})

	return response.blob()
}

export const downloadOrderFile = async (apiPath) => {
	const response = await apiRequest(apiPath, {
		method: 'GET',
		headers: {
			Accept: '*/*',
			'X-Requested-With': 'XMLHttpRequest'
		}
	})

	return response.blob()
}

export const updateOrder = async (orderId, data) => {
	return put(`/admin/orders/${orderId}`, data)
}

export const updateOrderShipping = async (orderId, data) => {
	return put(`/admin/orders/${orderId}/shipping`, data)
}

export const createOrder = async (data) => {
	return post('/admin/orders', data)
}

export const searchUserByEmailOrPhone = async (query) => {
	return get(`/admin/users?search=${encodeURIComponent(query)}`)
}

export const createUser = async (data) => {
	return post('/admin/users', data)
}

