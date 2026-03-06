<template>
	<div>
		<div>
			<a href="/" class="mb-40 max-w-290-px d-inline-block">
				<img :src="logoSrc" alt="Royal Element">
			</a>
			<h4 class="mb-12">Sign In to your Account</h4>
			<p class="mb-32 text-secondary-light text-lg">Welcome back! please enter your details</p>
		</div>

		<form @submit.prevent="handleSubmit">
			<div class="icon-field mb-16">
				<span class="icon top-50 translate-middle-y">
					<iconify-icon icon="mage:email"></iconify-icon>
				</span>
				<input
					v-model="email"
					type="email"
					class="form-control h-56-px bg-neutral-50 radius-12"
					placeholder="Email"
					required
				>
			</div>

			<div class="position-relative mb-20">
				<div class="icon-field">
					<span class="icon top-50 translate-middle-y">
						<iconify-icon icon="solar:lock-password-outline"></iconify-icon>
					</span>
					<input
						v-model="password"
						type="password"
						class="form-control h-56-px bg-neutral-50 radius-12"
						id="your-password"
						placeholder="Password"
						required
					>
				</div>
			</div>

			<div v-if="error" class="alert alert-danger py-8 px-12 mb-16 text-sm">
				{{ error }}
			</div>

			<button
				type="submit"
				class="btn btn-primary text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"
				:disabled="loading"
			>
				<span v-if="loading">Signing in...</span>
				<span v-else>Sign In</span>
			</button>
		</form>
	</div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { loginAdmin, getMe } from '@/api/adminAuth'
import { useAdminAuthStore } from '@/stores/adminAuth'
import { useErrorHandler } from '@/composables/useErrorHandler'

const router = useRouter()
const authStore = useAdminAuthStore()
const { handleFormError } = useErrorHandler()

const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const isDarkTheme = ref(false)

const logoSrc = computed(() => {
	return isDarkTheme.value
		? '/images/logo-white-with-gold-c.png'
		: '/images/logo-black-with-gold-c.png'
})

onMounted(() => {
	const savedTheme = localStorage.getItem('admin_theme')
	isDarkTheme.value = savedTheme === 'dark'

	const observer = new MutationObserver((mutations) => {
		mutations.forEach((mutation) => {
			if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
				const theme = document.documentElement.getAttribute('data-theme')
				isDarkTheme.value = theme === 'dark'
			}
		})
	})

	observer.observe(document.documentElement, {
		attributes: true,
		attributeFilter: ['data-theme']
	})

	const handleStorageChange = (e) => {
		if (e.key === 'admin_theme') {
			isDarkTheme.value = e.newValue === 'dark'
		}
	}

	window.addEventListener('storage', handleStorageChange)
})

const handleSubmit = async () => {
	loading.value = true
	error.value = ''

	try {
		const data = await loginAdmin({
			email: email.value,
			password: password.value
		})

		if (!data || !data.access_token) {
			throw new Error('Invalid response from server')
		}

		authStore.setToken(data.access_token)

		try {
			const userData = await getMe()
			authStore.setUser(userData)
		} catch (userError) {
		}

		const redirectPath = router.currentRoute.value.query.redirect || '/'
		await router.push(redirectPath)
	} catch (e) {
		const errorMsg = handleFormError(e, (msg) => {
			error.value = msg
		})
	} finally {
		loading.value = false
	}
}
</script>

<style scoped>
</style>


