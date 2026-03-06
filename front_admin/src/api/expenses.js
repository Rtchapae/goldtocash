
import { apiRequest, get } from './client'

export const fetchExpenses = async (params = {}) => {
	const queryParams = new URLSearchParams()
	
	if (params.page) queryParams.append('page', params.page)
	if (params.per_page) queryParams.append('per_page', params.per_page)
	if (params['order-by']) queryParams.append('order-by', params['order-by'])
	if (params['order-dir']) queryParams.append('order-dir', params['order-dir'])
	
	const endpoint = `/admin/expenses${queryParams.toString() ? '?' + queryParams.toString() : ''}`
	return get(endpoint)
}

export const exportExpensesToCsv = async () => {
	const response = await apiRequest('/admin/expenses/export', {
		method: 'GET'
	})
	
	if (!response.ok) {
		throw new Error('Failed to export expenses')
	}
	
	const blob = await response.blob()
	const url = window.URL.createObjectURL(blob)
	const a = document.createElement('a')
	a.href = url
	a.download = 'expenses.csv'
	document.body.appendChild(a)
	a.click()
	window.URL.revokeObjectURL(url)
	document.body.removeChild(a)
}

