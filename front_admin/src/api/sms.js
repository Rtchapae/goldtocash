import { post } from './client'

export const searchSms = async (filters = {}) => {
	try {
		const data = await post('/admin/sms/search', filters)
		return data
	} catch (error) {
		console.error('SMS search error:', error)
		throw error
	}
}