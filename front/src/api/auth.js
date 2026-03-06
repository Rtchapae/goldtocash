import { post, get } from './client.js'

export const login = async (credentials) => {
	return post('/front/auth/login', credentials, {}, true)
}

export const getMe = async () => {
	return get('/front/auth/me')
}

export const logout = async () => {
	try {
		const result = await post('/front/auth/logout')
		localStorage.removeItem('jwt_token')
		return result
	} catch (error) {
		localStorage.removeItem('jwt_token')
		throw error
	}
}

export const forgotPassword = async (payload) => {
	return post('/front/auth/forgot-password', payload, {}, true)
}

export const refreshToken = async () => {
	const data = await post('/front/auth/refresh')
	
	if (data.access_token) {
		localStorage.setItem('jwt_token', data.access_token)
	}

	return data
}

