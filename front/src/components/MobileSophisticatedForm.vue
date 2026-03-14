<template>
	<div class="sophisticated-form standard-form mobile-kit-form">
		<form
			ref="formRef"
			name="kit-form"
			class="standard-form"
			id="mobile-june-2023-form"
			novalidate
			@submit.prevent="handleSubmit"
		>
			<h2 class="mobile-form-title">
				Sell Gold Jewelry Online for Cash
				<br />
				<span class="mobile-form-bonus">Best Price Guaranteed!</span>
			</h2>
			<p class="mobile-form-subtitle">Get the highest payout in 24 hours!</p>

			<div class="control-group form-group">
				<input
					v-model="firstName"
					type="text"
					class="form-control"
					id="mobile-first-name"
					name="first_name"
					required
					data-validation-required-message="Please enter your first name."
				/>
				<label for="mobile-first-name" :class="{ hasValue: !!firstName }">First Name</label>
				<p class="help-block"></p>
			</div>
			<div class="control-group form-group">
				<input
					v-model="lastName"
					type="text"
					class="form-control"
					id="mobile-last-name"
					name="last_name"
					required
					data-validation-required-message="Please enter your last name."
				/>
				<label for="mobile-last-name" :class="{ hasValue: !!lastName }">Last Name</label>
				<p class="help-block"></p>
			</div>

			<div class="control-group form-group">
				<input
					v-model="email"
					type="email"
					class="form-control"
					id="mobile-email"
					name="email"
					required
					data-validation-required-message="Please enter your email address."
				/>
				<label for="mobile-email" :class="{ hasValue: !!email }">Email</label>
			</div>

			<div class="control-group form-group">
				<input
					ref="phoneInputRef"
					type="tel"
					class="form-control"
					id="mobile-phone"
					name="phone"
					required
					data-validation-required-message="Please enter your phone number."
				/>
				<label for="mobile-phone" :class="{ hasValue: !!phoneValue }">Phone</label>
			</div>

			<div class="control-group form-group address-input-wrap">
				<input
					ref="addressInputRef"
					v-model="address"
					type="text"
					class="form-control pac-target-input"
					id="mobile-address"
					name="address"
					required
					data-validation-required-message="Please enter your address."
					placeholder=" "
					autocomplete="address-line1"
				/>
				<label for="mobile-address" :class="{ hasValue: !!address }">Address</label>
			</div>

			<!-- Hidden: populated by Google Places, sent to backend -->
			<input type="hidden" v-model="address2" name="address2" />
			<input type="hidden" v-model="city" name="city" />
			<input type="hidden" v-model="state" name="state" />
			<input type="hidden" v-model="zip" name="zip" />
			<input type="hidden" name="country" value="USA" />

			<div v-if="showVerification" id="phone-verify" class="phone-verify-block">
				<div class="form-group">
					<label for="mobile-verification-code" class="verification-label">
						Verification Code from SMS
					</label>
					<div class="verification-code-container d-flex">
						<input
							v-for="(_, i) in 4"
							:key="i"
							type="text"
							:name="`code${i + 1}`"
							class="form-control verification-code-input"
							maxlength="1"
							autocomplete="off"
							inputmode="numeric"
							:ref="(el) => setCodeRef(el, i)"
							@input="onCodeInput($event, i)"
							@keydown.backspace="onCodeBackspace($event, i)"
						/>
						<button
							id="mobile-resendCodeButton"
							type="button"
							class="resend-code-btn"
							:disabled="resendCountdown > 0"
							@click="handleResendCode"
						>
							<span v-if="resendCountdown > 0">{{ resendCountdown }}s</span>
							<span v-else>Resend Code</span>
						</button>
					</div>
					<p class="help-block"></p>
				</div>
			</div>

			<div id="success" class="form-message-block">
				<div
					class="alert alert-warning"
					:class="{ hidden: !verificationMessage }"
					role="alert"
				>
					<span
						v-show="isVerificationLoading"
						class="spinner-border text-warning"
						role="status"
					></span>
					<span id="messageRegistration">{{ verificationMessage }}</span>
				</div>
			</div>

			<p class="consent">
				By submitting your information you agree to our
				<a href="/privacy-policy">Privacy Policy</a>
				and
				<a href="/terms-and-conditions">Terms and Conditions</a>
				, and provide consent to use your phone number for transactional text messages.
			</p>

			<button
				type="submit"
				class="btn-green w-100 btn-lg register-submit-btn"
				:disabled="isLoading || isVerificationLoading"
			>
				<span
					v-if="isLoading || isVerificationLoading"
					class="spinner-border spinner-border-sm me-2"
					role="status"
				></span>
				{{ submitButtonLabel }}
			</button>
		</form>

		<p class="free-shipping-text">
			<img src="/images/icon-truck.svg" class="mr-2 free-shipping-truck-icon" alt="" aria-hidden="true" />
			Free &amp; insured shipping
			<b>up to $100,000*</b>
		</p>
		<!-- Trust Pilot Micro Star — Figma 2704-1226 (Frame 515: star + Trustpilot) -->
		<div class="trustpilot-block trustpilot-micro">
			<span class="trustpilot-micro-excellent">Excellent</span>
			<img src="/images/trustpilot-stars-green.svg" alt="" class="trustpilot-micro-stars" width="107" height="20" />
			<span class="trustpilot-micro-brand-wrap">
				<svg class="trustpilot-micro-star-icon" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
					<path d="M7 0L8.5 5L14 5.5L9.5 9L11 14L7 11L3 14L4.5 9L0 5.5L5.5 5L7 0Z" fill="#11BA69"/>
				</svg>
				<span class="trustpilot-micro-brand">Trustpilot</span>
			</span>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { KIT_FORM_TEXTS } from '@/constants/kitForm'
import { useKitForm } from '@/composables/useKitForm'
import { usePhoneMask } from '@/composables/usePhoneMask'
import { useGooglePlacesAddress } from '@/composables/useGooglePlacesAddress'

const formRef = ref(null)
const { isLoading, submitForm } = useKitForm()
const { phoneValue, phoneInputRef, setupMask } = usePhoneMask()
const addressInputRef = ref(null)

const { init: initPlacesAddress } = useGooglePlacesAddress({
	inputRef: addressInputRef,
	inputId: 'mobile-address',
	onPlaceSelect: (place, addr) => {
		address.value = addr.street || addr.fullAddress
		address2.value = addr.address2 || ''
		city.value = addr.city || ''
		state.value = addr.state || ''
		zip.value = addr.zip || ''
	}
})

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const address = ref('')
const address2 = ref('')
const city = ref('')
const state = ref('')
const zip = ref('')

const showVerification = ref(false)
const resendCountdown = ref(0)
const isVerificationLoading = ref(false)
const verificationMessage = ref('')
const isCreateAccountMode = ref(false)

let resendIntervalId = null
let switchToCreateAccountTimeoutId = null

const submitButtonLabel = computed(() => {
	if (isCreateAccountMode.value) {
		return isLoading.value || isVerificationLoading.value
			? KIT_FORM_TEXTS.BUTTON_PROCESSING
			: KIT_FORM_TEXTS.BUTTON_CONTINUE_UNVERIFIED
	}
	return isLoading.value || isVerificationLoading.value
		? KIT_FORM_TEXTS.BUTTON_PROCESSING
		: KIT_FORM_TEXTS.BUTTON_REQUEST_KIT_MOBILE
})

const codeInputRefs = ref([])
const setCodeRef = (el, index) => {
	if (el) codeInputRefs.value[index] = el
}

const onCodeInput = (e, index) => {
	const input = e.target
	const value = input.value.replace(/\D/g, '').slice(0, 1)
	input.value = value
	if (value && index < 3 && codeInputRefs.value[index + 1]) {
		codeInputRefs.value[index + 1].focus()
	}
}

const onCodeBackspace = (e, index) => {
	if (e.key === 'Backspace' && !e.target.value && index > 0 && codeInputRefs.value[index - 1]) {
		codeInputRefs.value[index - 1].focus()
	}
}

function getFormData() {
	const form = formRef.value
	if (!form) return null
	const fd = new FormData(form)
	const cityVal = fd.get('city_other') || fd.get('city') || ''
	const countryVal = fd.get('country_other') || fd.get('country') || KIT_FORM_TEXTS.DEFAULT_COUNTRY
	return {
		first_name: fd.get('first_name'),
		last_name: fd.get('last_name'),
		email: fd.get('email'),
		phone: fd.get('phone'),
		address: fd.get('address'),
		address2: fd.get('address2') || '',
		city: cityVal,
		state: fd.get('state'),
		zip: fd.get('zip'),
		country: countryVal,
	}
}

function startResendCountdown() {
	if (resendIntervalId) clearInterval(resendIntervalId)
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

function clearVerificationTimers() {
	if (resendIntervalId) {
		clearInterval(resendIntervalId)
		resendIntervalId = null
	}
	if (switchToCreateAccountTimeoutId) {
		clearTimeout(switchToCreateAccountTimeoutId)
		switchToCreateAccountTimeoutId = null
	}
}

function resetContinueUnverified() {
	isCreateAccountMode.value = false
	if (switchToCreateAccountTimeoutId) {
		clearTimeout(switchToCreateAccountTimeoutId)
		switchToCreateAccountTimeoutId = null
	}
}

async function handleResendCode() {
	const data = getFormData()
	if (!data) return
	resetContinueUnverified()
	isVerificationLoading.value = true
	verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_SENDING
	try {
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
	} catch (err) {
		verificationMessage.value =
			err?.data?.message || err?.data?.error || err?.message || KIT_FORM_TEXTS.VERIFICATION_SEND_ERROR
	} finally {
		isVerificationLoading.value = false
	}
}

async function handleSubmit() {
	const form = formRef.value
	if (!form || !form.checkValidity()) {
		form?.reportValidity()
		return
	}

	const data = getFormData()
	if (!data) return

	if (showVerification.value) {
		const code1 = (form.querySelector('[name="code1"]')?.value || '').trim()
		const code2 = (form.querySelector('[name="code2"]')?.value || '').trim()
		const code3 = (form.querySelector('[name="code3"]')?.value || '').trim()
		const code4 = (form.querySelector('[name="code4"]')?.value || '').trim()

		if (!isCreateAccountMode.value) {
			if (!code1 || !code2 || !code3 || !code4) {
				verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_FILL_ALL
				return
			}
			data.verification_code = `${code1}${code2}${code3}${code4}`
		} else {
			data.allow_unverified = true
		}

		isVerificationLoading.value = true
		try {
			const response = await submitForm(data)
			if (response?.requires_verification) {
				verificationMessage.value =
					response?.message || KIT_FORM_TEXTS.VERIFICATION_INVALID_CODE
				if (!isCreateAccountMode.value && !switchToCreateAccountTimeoutId) {
					switchToCreateAccountTimeoutId = setTimeout(() => {
						isCreateAccountMode.value = true
					}, 10000)
				}
			} else if (response?.requires_login !== true) {
				verificationMessage.value =
					response?.message || 'Kit created. You can check your email or sign in to your account.'
				showVerification.value = false
			}
		} catch (err) {
			verificationMessage.value =
				err?.data?.message || err?.message || 'An error occurred. Please try again.'
		} finally {
			isVerificationLoading.value = false
		}
		return
	}

	isVerificationLoading.value = true
	verificationMessage.value = KIT_FORM_TEXTS.VERIFICATION_SENDING

	try {
		const response = await submitForm(data)
		if (response?.requires_verification) {
			if (response?.status === true) {
				showVerification.value = true
				resetContinueUnverified()
				verificationMessage.value =
					response?.message || KIT_FORM_TEXTS.VERIFICATION_ENTER_CODE
				startResendCountdown()
			} else {
				showVerification.value = false
				verificationMessage.value =
					response?.message || 'Failed to send verification code. Please try again.'
				clearVerificationTimers()
				resetContinueUnverified()
			}
		} else if (response?.requires_login) {
			verificationMessage.value =
				response?.message || 'Kit created. You can check your email or sign in to your account.'
			showVerification.value = false
		}
	} catch (err) {
		verificationMessage.value =
			err?.data?.message || err?.message || 'An error occurred. Please try again.'
		if (!switchToCreateAccountTimeoutId) {
			switchToCreateAccountTimeoutId = setTimeout(() => {
				isCreateAccountMode.value = true
			}, 10000)
		}
	} finally {
		isVerificationLoading.value = false
	}
}

onMounted(async () => {
	await nextTick()
	let attempts = 0
	const tryMask = () => {
		if (phoneInputRef.value) {
			setupMask(phoneInputRef.value)
		} else if (attempts < 5) {
			attempts++
			setTimeout(tryMask, 100)
		}
	}
	tryMask()
	// Один раз инициализируем Places с задержкой, чтобы DOM и инпут были готовы
	let placesAttempts = 0
	const tryInitPlaces = () => {
		const input = addressInputRef.value || document.getElementById('mobile-address')
		if (input && input.isConnected) {
			console.log('[Places] tryInitPlaces: input found, attempt', placesAttempts + 1)
			initPlacesAddress().catch((e) => console.warn('[Places] init error', e))
			return
		}
		if (placesAttempts < 25) {
			placesAttempts++
			setTimeout(tryInitPlaces, 200)
		} else {
			console.warn('[Places] tryInitPlaces: gave up after 25 attempts, input not found')
		}
	}
	setTimeout(tryInitPlaces, 400)
})

onUnmounted(() => {
	clearVerificationTimers()
})
</script>

<style scoped>
/* .mobile-form-title layout/font overridden in _main_page.scss per Figma 2704-1200 */
.mobile-form-title {
	margin-bottom: 24px;
}

.mobile-form-bonus {
	color: var(--primary);
}

.register-submit-btn {
	border-radius: 4px;
}

.free-shipping-text {
	color: #989898;
	text-align: center;
	width: 100%;
	margin: 20px 0;
	white-space: nowrap;
	font-size: 3vw;
}

.free-shipping-text img {
	width: 1em;
}

.free-shipping-text .free-shipping-truck-icon {
	width: 1em;
	height: 1em;
	vertical-align: -0.15em;
}

.trustpilot-block {
	max-width: 300px;
	margin: 0 auto;
}

.trustpilot-block img {
	width: 100%;
}

/* Trust Pilot Micro Star — Figma 2704-1226 */
.trustpilot-micro {
	display: flex;
	flex-direction: row;
	justify-content: center;
	align-items: center;
	gap: 13px;
	text-align: center;
}

.trustpilot-micro-excellent,
.trustpilot-micro-brand {
	font-family: Roboto, sans-serif;
	font-weight: 400;
	font-size: 12px;
	line-height: 2.17;
	color: #000;
}

.trustpilot-micro-brand-wrap {
	display: flex;
	flex-direction: row;
	align-items: center;
	justify-content: center;
	gap: 4px;
}

.trustpilot-micro-star-icon {
	flex-shrink: 0;
}

.trustpilot-micro-stars {
	width: 107px;
	height: 20px;
	flex-shrink: 0;
}

.phone-verify-block {
	margin-top: 16px;
}

.form-message-block {
	margin-top: 12px;
}

.verification-label {
	display: block;
	margin-bottom: 8px;
	font-weight: 500;
	color: #333;
}

.verification-code-container {
	gap: 8px;
	align-items: center;
	flex-wrap: wrap;
	margin-top: 8px;
	margin-bottom: 8px;
}

.verification-code-input {
	width: 50px;
	height: 50px;
	text-align: center;
	font-size: 20px;
	font-weight: 600;
	padding: 0;
	border: 2px solid #ddd;
	border-radius: 8px;
}

.verification-code-input:focus {
	border-color: var(--primary, #007bff);
	outline: none;
}

.resend-code-btn {
	min-width: 120px;
	height: 50px;
	border: 2px solid #ddd;
	border-radius: 8px;
	font-weight: 500;
	background: #fff;
	cursor: pointer;
}

.resend-code-btn:disabled {
	opacity: 0.6;
	cursor: not-allowed;
}

.consent {
	font-size: 12px;
}

.hidden {
	display: none !important;
}

@media (min-width: 600px) {
	.free-shipping-text {
		font-size: 16px !important;
	}
}
</style>
