import { get, post } from './client'

export const getChats = async () => {
	return get('/admin/messages')
}

export const getThreadMessages = async (threadId) => {
	return get(`/admin/messages/${threadId}`)
}

export const sendMessage = async (threadId, text) => {
	return post(`/admin/messages/${threadId}/send`, { text })
}

