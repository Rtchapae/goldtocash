import { post, get } from './client'

export const loginAdmin = async (credentials) => {
	return post('/admin/auth/login', credentials, {}, true)
}

export const getMe = async () => {
	return get('/admin/auth/me')
}

export const logoutAdmin = async () => {
	try {
		const result = await post('/admin/auth/logout')
		if (typeof window !== 'undefined') {
			window.localStorage.removeItem('admin_access_token')
		}
		return result
	} catch (error) {
		if (typeof window !== 'undefined') {
			window.localStorage.removeItem('admin_access_token')
		}
		throw error
	}
}

export const refreshToken = async () => {
	const data = await post('/admin/auth/refresh')

	if (data.access_token && typeof window !== 'undefined') {
		window.localStorage.setItem('admin_access_token', data.access_token)
	}

	return data
}





