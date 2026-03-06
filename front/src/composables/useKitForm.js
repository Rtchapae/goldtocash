import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { registerKit } from '@/api/kitRegistration'
import { useToast } from '@/composables/useToast'

export function useKitForm() {
	const router = useRouter()
	const toast = useToast()
	const isLoading = ref(false)
	const error = ref(null)

	const submitForm = async (formData) => {
		isLoading.value = true
		error.value = null

		try {
			const response = await registerKit(formData)

			if (response.requires_verification) {
				return response
			}

			if (response.status) {
				if (response.access_token) {
					localStorage.setItem('jwt_token', response.access_token)

					toast.success('Kit request submitted successfully! Redirecting...')

					const firstName = formData.first_name?.trim() || ''
					const lastName = formData.last_name?.trim() || ''
					const userName = firstName && lastName
						? `${firstName} ${lastName}`.toUpperCase()
						: (firstName || lastName || 'USER').toUpperCase()
					const queryParams = new URLSearchParams({
						name: userName,
						order_id: response.order_id || '',
						email: formData.email || '',
						password: response.password || '',
					})

					setTimeout(() => {
						router.push(`/user/kit-request-success?${queryParams.toString()}`)
					}, 500)
					return response
				} else {
					toast.success(response.message || 'Kit created successfully.')
					return response
				}
			} else {
				const errorMsg = response.error || response.message || 'Registration failed'
				throw new Error(errorMsg)
			}
		} catch (err) {
			const errorMsg = err?.message || err?.data?.message || 'An error occurred during registration. Please try again.'
			error.value = errorMsg
			toast.error(errorMsg)
			throw err
		} finally {
			isLoading.value = false
		}
	}

	return {
		isLoading,
		error,
		submitForm,
	}
}
