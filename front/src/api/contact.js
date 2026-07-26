import { post } from './client.js'

/**
 * Legacy contact form — same fields as action="/sendmail" (name, email, phone, message).
 */
export const sendContactMessage = async (payload) =>
	post('/sendmail', {
		name: payload.name,
		email: payload.email,
		phone: payload.phone,
		message: payload.message,
	})
