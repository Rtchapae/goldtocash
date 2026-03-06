import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useAdminAuthStore = defineStore('adminAuth', () => {
	const token = ref(null)
	const user = ref(null)

	const loadFromStorage = () => {
		if (typeof window === 'undefined') return
		const stored = window.localStorage.getItem('admin_access_token')
		token.value = stored || null
	}

	const setToken = (newToken) => {
		token.value = newToken
		if (typeof window === 'undefined') return
		if (newToken) {
			window.localStorage.setItem('admin_access_token', newToken)
		} else {
			window.localStorage.removeItem('admin_access_token')
		}
	}

	const setUser = (userData) => {
		user.value = userData
	}

	const clearToken = () => {
		setToken(null)
		user.value = null
	}

	const isAuthenticated = computed(() => {
		return !!token.value
	})

	const userName = computed(() => {
		if (!user.value) return 'Admin'
		if (user.value.name) return user.value.name
		if (user.value.first_name || user.value.last_name) {
			return `${user.value.first_name || ''} ${user.value.last_name || ''}`.trim()
		}
		return user.value.email?.split('@')[0] || 'Admin'
	})

	const userRole = computed(() => {
		if (!user.value) return 'Admin'
		return user.value.role || 'Admin'
	})

	loadFromStorage()

	return {
		token,
		user,
		setToken,
		setUser,
		clearToken,
		isAuthenticated,
		userName,
		userRole
	}
})





