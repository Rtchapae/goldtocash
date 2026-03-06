<template>
    <section class="section-login">
        <div class="row w-100">
            <div class="col-lg-6 mx-auto">
                <div class="auto-form-wrapper">
                    <form id="main-login-form" @submit.prevent="handleSubmit">
                        <div v-if="successMessage" class="alert alert-success" role="alert">
                            {{ successMessage }}
                        </div>
                        <div v-if="errorMessage" class="alert alert-danger" role="alert">
                            {{ errorMessage }}
                        </div>
                        <div class="login-box-msg">You forgot your password? Here you can easily retrieve a new
                            password.</div>
                        <div class="form-group">
                            <label class="label d-flex justify-content-between">
                                <div>Username</div>
                                <div><a class="forgot-password" href="/sign-in">Back to login</a>
                                </div>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">@</div>
                                </div>
                                <input
                                    v-model="email"
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    placeholder="Your Email"
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
                                {{ isLoading ? 'Sending...' : 'Send Password Reset Link' }}
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
import { forgotPassword } from '@/api/auth'
import { useErrorHandler } from '@/composables/useErrorHandler'
import AuthAssurances from '@/components/auth/AuthAssurances.vue'

const { handleFormError } = useErrorHandler()
const email = ref('')
const isLoading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

async function handleSubmit() {
    errorMessage.value = ''
    successMessage.value = ''
    isLoading.value = true
    try {
        const data = await forgotPassword({ email: email.value })
        successMessage.value = data.message || 'If an account exists for this email, you will receive a new password shortly.'
    } catch (err) {
        handleFormError(err, (msg) => { errorMessage.value = msg })
    } finally {
        isLoading.value = false
    }
}
</script>
