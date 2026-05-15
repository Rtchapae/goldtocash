import { getMe } from './auth.js'
import { get, put, post, postFormData } from './client.js'
import { collectKitAttribution } from './kitRegistration.js'

export const getUserProfile = async () => {
	return getMe()
}

export const getUserOrders = async () => {
	return get('/front/user/orders')
}

export const updateUserProfile = async (payload) => {
	return put('/front/user/profile', payload)
}

export const getUserDocuments = async () => {
	return get('/front/user/documents')
}

export const uploadDocument = async (formData) => {
	return postFormData('/front/user/documents/upload', formData)
}

export const getMessages = async () => {
	return get('/front/user/messages')
}

export const sendMessage = async (text) => {
	return post('/front/user/messages', { text })
}

export const markMessagesAsRead = async () => {
	return post('/front/user/messages/mark-read')
}

export const createKitRequest = async () => {
	return post('/front/user/orders/create', collectKitAttribution())
}

export const respondToOffer = async (orderId, action) => {
	return post(`/front/user/orders/${orderId}/offer-response`, { action })
}

