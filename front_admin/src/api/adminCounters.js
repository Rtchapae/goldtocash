import { get } from './client'

export const getCounters = async () => {
	return get('/admin/counters')
}

export const getDashboardAnalytics = async (params = {}) => {
	const queryParams = new URLSearchParams(params)
	return get(`/admin/counters/analytics?${queryParams}`)
}

