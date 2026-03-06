import { post } from './client'

export function sendVerificationCode(phone) {
	return post('/user/send-code', { phone })
}

export function verifyVerificationCode(phone, code) {
	return post('/user/verify-code', { phone, code })
}


