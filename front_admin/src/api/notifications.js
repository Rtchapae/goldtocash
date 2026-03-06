import { get, post } from './client'

export const fetchNotifications = async (params = {}) => {
	const queryParams = new URLSearchParams(params)
	return get(`/admin/notifications?${queryParams}`)
}

export const markNotificationsAsRead = async () => {
	return post('/admin/notifications/mark-read')
}

export const markNotificationAsRead = async (notificationId) => {
	return post(`/admin/notifications/${notificationId}/mark-read`)
}

export const getNotificationCounts = async () => {
	return get('/admin/notifications/count')
}

