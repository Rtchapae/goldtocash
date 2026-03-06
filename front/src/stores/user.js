import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getUserProfile as apiGetUserProfile } from '@/api/userAccount'

export const useUserStore = defineStore('user', () => {
	const profile = ref(null)
	const isLoading = ref(false)
	const error = ref(null)
	const unreadMessagesCount = ref(0)

	const loadProfile = async () => {
		if (isLoading.value || profile.value) {
			return profile.value
		}

		isLoading.value = true
		error.value = null

		try {
			const data = await apiGetUserProfile()
			profile.value = data
			return profile.value
		} catch (err) {
			error.value = err
			throw err
		} finally {
			isLoading.value = false
		}
	}

	const setProfile = (data) => {
		profile.value = data
	}

	const setUnreadMessagesCount = (count) => {
		unreadMessagesCount.value = count
	}

	return {
		profile,
		isLoading,
		error,
		unreadMessagesCount,
		loadProfile,
		setProfile,
		setUnreadMessagesCount,
	}
})




