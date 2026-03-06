<template>
	<section class="section-login">
		<div class="row w-100">
			<div class="col-lg-6 mx-auto">
				<div class="auto-form-wrapper">
					<form id="main-login-form" @submit.prevent="handleLogin">
						<div v-if="errorMessage" class="alert alert-danger" role="alert">
							{{ errorMessage }}
						</div>
						<div class="form-group">
							<label class="label">Username</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text">@</div>
								</div>
								<input 
									type="email" 
									name="email" 
									v-model="email"
									class="form-control"
									placeholder="Your Email"
									required
									:disabled="isLoading"
								>
							</div>
						</div>
						<div class="form-group">
							<label class="label d-flex justify-content-between">
								<div>Password</div>
								<div><a class="forgot-password" href="/password/reset">Forgot your password?</a></div>
							</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<div class="input-group-text"><i class="bi bi-key"></i></div>
								</div>
								<input 
									type="password" 
									name="password" 
									v-model="password"
									class="form-control" 
									placeholder="*********"
									required
									:disabled="isLoading"
								>
							</div>
						</div>
						<div class="form-group">
							<button 
								type="submit" 
								class="btn btn-primary submit-btn btn-block"
								:disabled="isLoading"
							>
								<span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
								{{ isLoading ? 'Logging in...' : 'Login' }}
							</button>
						</div>
					</form>
				</div>
				<AuthAssurances />
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { login } from '@/api/auth'
import { useToast } from '@/composables/useToast'
import { useErrorHandler } from '@/composables/useErrorHandler'
import AuthAssurances from '@/components/auth/AuthAssurances.vue'

const router = useRouter()
const route = useRoute()
const toast = useToast()
const { handleFormError } = useErrorHandler()

const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

const handleLogin = async () => {
	errorMessage.value = ''
	isLoading.value = true

	try {
		const response = await login({
			email: email.value,
			password: password.value,
		})

		if (response.access_token) {
			// Save JWT token to localStorage
			localStorage.setItem('jwt_token', response.access_token)
			
			toast.success('Login successful! Redirecting...')
			
			// Redirect to intended page or default to user account
			const redirectPath = route.query.redirect || '/user/account'
			setTimeout(() => {
				router.push(redirectPath)
			}, 500)
		} else {
			const errorMsg = 'Login failed. Please try again.'
			errorMessage.value = errorMsg
			toast.error(errorMsg)
		}
	} catch (error) {
		// Use error handler for consistent error handling
		const errorMsg = handleFormError(error, (msg) => {
			errorMessage.value = msg
		})
	} finally {
		isLoading.value = false
	}
}
</script>

<style scoped>
.alert {
	margin-bottom: 1rem;
}

.spinner-border-sm {
	width: 1rem;
	height: 1rem;
}
</style>
