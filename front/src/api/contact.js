import { get, post } from './client.js'

/**
 * Legacy contact form — same fields as action="/sendmail" (name, email, phone, message).
 * Captcha fields are signed by GET /sendmail/captcha.
 */
export const getContactCaptcha = async () => {
	const data = await get('/sendmail/captcha')
	return data?.data
}

export const sendContactMessage = async (payload) =>
	post('/sendmail', {
		name: payload.name,
		email: payload.email,
		phone: payload.phone,
		message: payload.message,
		captcha_a: payload.captcha_a,
		captcha_b: payload.captcha_b,
		captcha_expires: payload.captcha_expires,
		captcha_token: payload.captcha_token,
		captcha_answer: payload.captcha_answer,
	})
