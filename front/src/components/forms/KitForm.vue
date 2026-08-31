<template>
	<div>
		<!-- Mobile version (v-if: one address field in DOM — avoids Places on hidden input) -->
		<div v-if="kitViewport === 'mobile'" :class="mobileWrapperClass">
			<div class="kit-form">
				<form :name="mobileFormName" :id="mobileFormId" class="standard-form" @submit.prevent="handleSubmit">
					<input type="hidden" name="_token" value="">
					<h2 class="kit-form-title">
						{{ KIT_FORM_TEXTS.TITLE }}
					</h2>
				<FormContent
					:is-mobile="true"
					:show-verification="showVerification"
					:resend-countdown="resendCountdown"
					:verification-message="verificationMessage"
					:is-verification-loading="isVerificationLoading"
					@resend-code="handleResendCode"
				/>
					<p class="consent">
						{{ KIT_FORM_TEXTS.CONSENT_PREFIX }}
						<a :href="KIT_FORM_TEXTS.LINK_PRIVACY_POLICY">{{ KIT_FORM_TEXTS.LINK_PRIVACY_POLICY_TEXT }}</a>
						{{ KIT_FORM_TEXTS.CONSENT_AND }}
						<a :href="KIT_FORM_TEXTS.LINK_TERMS">{{ KIT_FORM_TEXTS.LINK_TERMS_TEXT }}</a>
						,
						{{ KIT_FORM_TEXTS.CONSENT_PHONE }}
					</p>
					<button
						type="submit"
						class="btn btn-green w-100 btn-lg kit-form-submit-btn"
						:disabled="isLoading || isVerificationLoading"
					>
						<span
							v-if="isLoading || isVerificationLoading"
							class="spinner-border spinner-border-sm me-2"
							role="status"
						></span>
						{{ submitButtonLabelMobile }}
					</button>
					<div v-if="error" class="alert alert-danger mt-2" role="alert">
						{{ error }}
					</div>
				</form>
				<p class="free-shipping-text">
					<img src="/images/truck-white.png" class="mr-2 free-shipping-icon" />
					{{ KIT_FORM_TEXTS.SHIPPING_FREE }}
					<b>{{ KIT_FORM_TEXTS.SHIPPING_UP_TO }}</b>
				</p>
				<div class="trustpilot-mobile">
					<img src="/images/trustpilot-horizontal-green-black-text.png" class="trustpilot-mobile-img" />
				</div>
			</div>
		</div>
		<!-- Desktop version -->
		<form
			v-if="kitViewport === 'desktop'"
			:name="desktopFormName"
			:id="desktopFormId"
			:class="desktopFormClass"
			@submit.prevent="handleSubmit"
		>
			<input type="hidden" name="_token" value="">
			<h3>
				{{ KIT_FORM_TEXTS.TITLE }}
			</h3>
			<FormContent
				:is-mobile="false"
				:show-verification="showVerification"
				:resend-countdown="resendCountdown"
				:verification-message="verificationMessage"
				:is-verification-loading="isVerificationLoading"
				@resend-code="handleResendCode"
			/>
			<p class="consent">
				{{ KIT_FORM_TEXTS.CONSENT_PREFIX }}
				<a :href="KIT_FORM_TEXTS.LINK_PRIVACY_POLICY">{{ KIT_FORM_TEXTS.LINK_PRIVACY_POLICY_TEXT }}</a>
				{{ KIT_FORM_TEXTS.CONSENT_AND }}
				<a :href="KIT_FORM_TEXTS.LINK_TERMS">{{ KIT_FORM_TEXTS.LINK_TERMS_TEXT }}</a>.
			</p>
			<button
				type="submit"
				class="btn btn-primary"
				:disabled="isLoading || isVerificationLoading"
			>
				<span
					v-if="isLoading || isVerificationLoading"
					class="spinner-border spinner-border-sm me-2"
					role="status"
				></span>
				{{ submitButtonLabelDesktop }}
			</button>
			<div v-if="error" class="alert alert-danger mt-2" role="alert">
				{{ error }}
			</div>
			<div class="trustpilot-form-medal">
				<img src="/images/trustpilot_form.svg" class="trustpilot-medal-img" />
			</div>
		</form>
	</div>
</template>

<script setup>
import { computed, onBeforeMount, onMounted, onBeforeUnmount, onUnmounted, ref } from 'vue'
import FormContent from './KitFormContent.vue'
import { useKitForm } from '@/composables/useKitForm'
import { buildKitPayload } from '@/api/kitRegistration'
import { KIT_FORM_TEXTS } from '@/constants/kitForm'

const props = defineProps({
	mobile: {
		type: Boolean,
		default: null // null means auto-detect
	},
	inline: {
		type: Boolean,
		default: false
	}
})

const mobileWrapperClass = computed(() => {
	if (props.inline) {
		return 'kit-form-mobile-wrapper d-lg-none'
	}
	if (props.mobile === true) {
		return 'kit-form-mobile-wrapper d-md-none'
	} else if (props.mobile === false) {
		return 'kit-form-mobile-wrapper d-none'
	}
	// Auto-detect: show mobile on small screens
	return 'kit-form-mobile-wrapper d-md-none'
})

const desktopFormClass = computed(() => {
	if (props.inline) {
		return 'standard-form'
	}
	return 'standard-form'
})

/** Only mount one kit form variant so Google Places binds to the visible address input. */
const kitViewport = ref(props.mobile === true ? 'mobile' : 'desktop')

function updateKitViewport() {
	if (typeof window === 'undefined') return
	if (props.mobile === true) {
		kitViewport.value = 'mobile'
		return
	}
	if (props.mobile === false) {
		kitViewport.value = 'desktop'
		return
	}
	kitViewport.value = window.innerWidth >= 992 ? 'desktop' : 'mobile'
}

onBeforeMount(() => {
	updateKitViewport()
})

onMounted(() => {
	updateKitViewport()
	window.addEventListener('resize', updateKitViewport)
})

onBeforeUnmount(() => {
	if (typeof window !== 'undefined') {
		window.removeEventListener('resize', updateKitViewport)
	}
})

const mobileFormName = KIT_FORM_TEXTS.FORM_MOBILE_NAME
const mobileFormId = KIT_FORM_TEXTS.FORM_MOBILE_ID
const desktopFormName = KIT_FORM_TEXTS.FORM_DESKTOP_NAME
const desktopFormId = KIT_FORM_TEXTS.FORM_DESKTOP_ID

const { isLoading, error, submitForm } = useKitForm()

const showVerification = ref(false)
const resendCountdown = ref(0)
const isVerificationLoading = ref(false)
const verificationMessage = ref('')
const isCreateAccountMode = ref(false)
let resendIntervalId = null
let switchToCreateAccountTimeoutId = null

const submitButtonLabelDesktop = computed(() => {
	if (isCreateAccountMode.value) {
		return isLoading.value || isVerificationLoading.value ? KIT_FORM_TEXTS.BUTTON_PROCESSING : KIT_FORM_TEXTS.BUTTON_CONTINUE_UNVERIFIED
	}
	return isLoading.value || isVerificationLoading.value ? KIT_FORM_TEXTS.BUTTON_PROCESSING : KIT_FORM_TEXTS.BUTTON_REQUEST_KIT_DESKTOP
})

const submitButtonLabelMobile = computed(() => {
	if (isCreateAccountMode.value) {
		return isLoading.value || isVerificationLoading.value ? KIT_FORM_TEXTS.BUTTON_PROCESSING : KIT_FORM_TEXTS.BUTTON_CONTINUE_UNVERIFIED
	}
	return isLoading.value || isVerificationLoading.value ? KIT_FORM_TEXTS.BUTTON_PROCESSING : KIT_FORM_TEXTS.BUTTON_REQUEST_KIT_MOBILE
})

const startResendCountdown = () => {
	if (resendIntervalId) {
		clearInterval(resendIntervalId)
	}
	resendCountdown.value = 60
	resendIntervalId = setInterval(() => {
		if (resendCountdown.value <= 0) {
			clearInterval(resendIntervalId)
			resendIntervalId = null
			return
		}
		resendCountdown.value -= 1
	}, 1000)
}

const clearVerificationTimers = () => {
	if (resendIntervalId) {
		clearInterval(resendIntervalId)
		resendIntervalId = null
	}
	if (switchToCreateAccountTimeoutId) {
		clearTimeout(switchToCreateAccountTimeoutId)
		switchToCreateAccountTimeoutId = null
	}
}

const resetContinueUnverified = () => {
	isCreateAccountMode.value = false
	if (switchToCreateAccountTimeoutId) {
		clearTimeout(switchToCreateAccountTimeoutId)
		switchToCreateAccountTimeoutId = null
	}
}

const handleResendCode = async () => {
	const mobileForm = document.getElementById(mobileFormId)
	const desktopForm = document.getElementById(desktopFormId)
	const isVisible = (el) => !!(el && el.offsetParent)
	const form = isVisible(desktopForm) ? desktopForm : (isVisible(mobileForm) ? mobileForm : (desktopForm || mobileForm))
	
	if (!form) return
	
	const formData = new FormData(form)

	resetContinueUnverified()
	
	isVerificationLoading.value = true
	verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_SENDING
	
	try {
		const cityValue = formData.get('city_other') || formData.get('city') || ''
		const countryValue = formData.get('country_other') || formData.get('country') || KIT_FORM_TEXTS.DEFAULT_COUNTRY
		
		const data = buildKitPayload({
			first_name: formData.get('first_name'),
			last_name: formData.get('last_name'),
			email: formData.get('email'),
			phone: formData.get('phone'),
			address: formData.get('address'),
			address2: formData.get('address2') || '',
			city: cityValue,
			state: formData.get('state'),
			zip: formData.get('zip'),
			country: countryValue,
			street: formData.get('street'),
			fullAddress: formData.get('fullAddress'),
		})

		const response = await submitForm(data)
		
		if (response?.requires_verification) {
			if (response?.status === true) {
				verificationMessage.value = response?.message || KIT_FORM_TEXTS.VERIFICATION_CODE_SENT
				startResendCountdown()
			} else {
				verificationMessage.value = response?.message || KIT_FORM_TEXTS.VERIFICATION_SEND_ERROR
			}
		} else {
			verificationMessage.value = response?.message || KIT_FORM_TEXTS.VERIFICATION_SEND_ERROR
		}
	} catch (error) {
		console.error('Failed to resend verification code:', error)
		const errorMessage = error?.data?.message || error?.data?.error || error?.message || KIT_FORM_TEXTS.VERIFICATION_SEND_ERROR
		verificationMessage.value = errorMessage
	} finally {
		isVerificationLoading.value = false
	}
}

const handleSubmit = async (event) => {
	const form = event.target
	if (!form) return

	if (!form.checkValidity()) {
		form.reportValidity()
		return
	}

	const formData = new FormData(form)

	if (showVerification.value) {
		const cityValue = formData.get('city_other') || formData.get('city') || ''
		const countryValue = formData.get('country_other') || formData.get('country') || KIT_FORM_TEXTS.DEFAULT_COUNTRY

		const baseData = {
			first_name: formData.get('first_name'),
			last_name: formData.get('last_name'),
			email: formData.get('email'),
			phone: formData.get('phone'),
			address: formData.get('address'),
			address2: formData.get('address2') || '',
			city: cityValue,
			state: formData.get('state'),
			zip: formData.get('zip'),
			country: countryValue,
		}

		let data

		if (isCreateAccountMode.value) {
			data = {
				...baseData,
				allow_unverified: true,
			}
		} else {
			const enteredCode = (formData.get('verification_code') || '').toString().trim()

			if (!enteredCode || enteredCode.length !== 4) {
				verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_FILL_ALL
				return
			}

			data = {
				...baseData,
				verification_code: enteredCode,
			}
		}

		isVerificationLoading.value = true

		try {
			console.log('[KitForm modal] submit data:', { ...data })
			const response = await submitForm(buildKitPayload(data))
			
		if (response?.requires_verification) {
			verificationMessage.value = response?.message || KIT_FORM_TEXTS.VERIFICATION_INVALID_CODE

			if (!isCreateAccountMode.value && data?.verification_code && !switchToCreateAccountTimeoutId) {
				switchToCreateAccountTimeoutId = setTimeout(() => {
					isCreateAccountMode.value = true
				}, 10000)
			}
		} else if (response?.requires_login) {
				verificationMessage.value = response?.message || 'Kit created. You can check your email or sign in to your account.'
				showVerification.value = false
			}
		} catch (err) {
			console.error('Form submission error:', err)
			const errorMsg = err?.data?.message || err?.message || 'An error occurred. Please try again.'
			verificationMessage.value = errorMsg
		} finally {
			isVerificationLoading.value = false
		}

		return
	}

	isVerificationLoading.value = true
	verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_SENDING

	try {
		const cityValue = formData.get('city_other') || formData.get('city') || ''
		const countryValue = formData.get('country_other') || formData.get('country') || KIT_FORM_TEXTS.DEFAULT_COUNTRY
		
		const data = buildKitPayload({
			first_name: formData.get('first_name'),
			last_name: formData.get('last_name'),
			email: formData.get('email'),
			phone: formData.get('phone'),
			address: formData.get('address'),
			address2: formData.get('address2') || '',
			city: cityValue,
			state: formData.get('state'),
			zip: formData.get('zip'),
			country: countryValue,
			street: formData.get('street'),
			fullAddress: formData.get('fullAddress'),
		})

		console.log('[KitForm modal] submit data:', { ...data })
		const response = await submitForm(data)

		if (response?.requires_verification) {
			if (response?.status === true) {
				showVerification.value = true
				resetContinueUnverified()
				verificationMessage.value = response?.message || KIT_FORM_TEXTS.VERIFICATION_ENTER_CODE
				startResendCountdown()
			} else {
				showVerification.value = false
				verificationMessage.value = response?.message || 'Failed to send verification code. Please try again.'
				clearVerificationTimers()
				resetContinueUnverified()
			}
		} else if (response?.requires_login) {
			verificationMessage.value = response?.message || 'Kit created. You can check your email or sign in to your account.'
			showVerification.value = false
		}
	} catch (err) {
		console.error('Form submission error:', err)
		const errorMsg = err?.data?.message || err?.message || 'An error occurred. Please try again.'
		verificationMessage.value = errorMsg
		
		if (!switchToCreateAccountTimeoutId) {
			switchToCreateAccountTimeoutId = setTimeout(() => {
				isCreateAccountMode.value = true
			}, 10000)
		}
	} finally {
		isVerificationLoading.value = false
	}
}

onUnmounted(() => {
	clearVerificationTimers()
})
</script>

<style scoped>
.kit-form-mobile-wrapper {
	padding-top: 30px;
}

.kit-form p.consent {
	font-size: 12px;
}

.kit-form-title {
	text-align: center;
	margin-bottom: 30px;
}

.kit-form-submit-btn {
	border-radius: 25px;
}

.free-shipping-text {
	color: #989898;
	text-align: center;
	width: 100%;
	margin: 20px 0;
	white-space: nowrap;
	font-size: 3vw;
}

.free-shipping-icon {
	width: 1.3em;
	filter: invert(.4);
}

.trustpilot-mobile {
	text-align: center;
	margin-top: 1rem;
	max-width: 300px;
	margin-left: auto;
	margin-right: auto;
}

.trustpilot-mobile-img {
	width: 100%;
}

.trustpilot-medal-img {
	width: 100%;
	max-width: 250px;
	margin: 0;
}

@media (min-width: 600px) {
	.free-shipping-text {
		font-size: 16px !important;
	}
}
</style>
