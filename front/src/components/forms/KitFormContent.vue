<template>
	<div>
		<div class="row">
			<div class="col-6">
				<div class="control-group form-group">
					<input 
						type="text" 
						class="form-control" 
						:id="fieldId('first-name')" 
						name="first_name"
						v-model="firstName"
						required
						:data-validation-required-message="isMobile ? 'Please enter your first name.' : ''">
					<label :for="fieldId('first-name')" :class="{ hasValue: firstName }">First Name</label>
					<p class="help-block"></p>
				</div>
			</div>
			<div class="col-6">
				<div class="control-group form-group">
					<input 
						type="text" 
						class="form-control" 
						:id="fieldId('last-name')" 
						name="last_name"
						v-model="lastName"
						required
						:data-validation-required-message="isMobile ? 'Please enter your last name.' : ''">
					<label :for="fieldId('last-name')" :class="{ hasValue: lastName }">Last Name</label>
					<p class="help-block"></p>
				</div>
			</div>
		</div>
		<div class="control-group form-group">
			<input 
				type="email" 
				class="form-control" 
				:id="fieldId('email')" 
				name="email"
				v-model="email"
				required
				:data-validation-required-message="isMobile ? 'Please enter your email address.' : ''">
			<label :for="fieldId('email')" :class="{ hasValue: email }">Email</label>
		</div>
		<div class="control-group form-group">
			<input 
				ref="phoneInputRef"
				type="tel" 
				class="form-control" 
				:id="fieldId('phone')" 
				name="phone" 
				required
				:data-validation-required-message="isMobile ? 'Please enter your phone number.' : ''">
			<label :for="fieldId('phone')" :class="{ hasValue: phoneValue }">Phone</label>
		</div>
		<div class="control-group form-group address-input-wrap">
			<input
				ref="addressInputRef"
				type="text"
				class="form-control pac-target-input"
				:id="fieldId('address')"
				v-model="fullAddress"
				required
				:data-validation-required-message="isMobile ? 'Please enter your address.' : ''"
				placeholder=""
				autocomplete="off"
			/>
			<label :for="fieldId('address')" :class="{ hasValue: fullAddress }">Address</label>
		</div>
		<!-- Hidden: populated by Google Places or fallback to fullAddress -->
		<input type="hidden" name="address" :value="address || fullAddress" />
		<input type="hidden" name="address2" :value="address2" />
		<input type="hidden" name="state" :value="state" />
		<input type="hidden" name="city" :value="city" />
		<template v-if="!isMobile">
			<div class="control-group form-group">
				<select 
					v-model="selectedCountry" 
					name="country" 
					:id="fieldId('country')" 
					class="form-control"
					@change="onCountryChange">
					<option value="">Select country</option>
					<option v-for="country in countries" :key="country.code" :value="country.code">{{ country.name }}</option>
					<option value="__other__">Other (enter manually)</option>
				</select>
				<input
					v-if="selectedCountry === '__other__'"
					v-model="countryOther"
					type="text"
					class="form-control mt-2"
					name="country_other"
					placeholder="Enter country name"
					maxlength="100">
				<label :for="fieldId('country')" :class="{ hasValue: selectedCountry && selectedCountry !== '__other__' }">Country</label>
			</div>
		</template>
		<input v-else type="hidden" name="country" :value="KIT_FORM_TEXTS.DEFAULT_COUNTRY">
		<input type="hidden" name="zip" :value="zip" />
		<div id="phone-verify" v-if="showVerification">
			<div class="form-group">
				<label :for="fieldId('verification-code')" class="verification-label">Verification Code from SMS</label>
				<div :id="fieldId('verification-code-container')" class="verification-code-container">
					<input
						type="text"
						name="verification_code"
						class="form-control verification-code-input verification-code-single"
						maxlength="4"
						autocomplete="off"
						inputmode="numeric"
						pattern="[0-9]{4}"
						placeholder="0000"
						v-model="verificationCode"
					>
					<button
						:id="fieldId('resendCodeButton')"
						type="button"
						class="btn btn-white resend-code-btn"
						:disabled="isResendDisabled"
						@click="$emit('resend-code')"
					>
						<span v-if="resendCountdown > 0">{{ resendCountdown }}s</span>
						<span v-else>Resend Code</span>
					</button>
				</div>
				<p v-if="isMobile" class="help-block"></p>
			</div>
		</div>
		<div id="success">
			<div
				class="alert alert-warning"
				:class="{ hidden: !verificationMessage }"
				role="alert"
			>
				<span
					class="spinner-border text-warning"
					role="status"
					v-show="isVerificationLoading"
				></span>
				<span :id="fieldId('messageRegistration')">
					{{ verificationMessage }}
				</span>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, watch, nextTick, computed } from 'vue'
import { US_STATES } from '@/constants/states'
import { KIT_FORM_TEXTS } from '@/constants/kitForm'
import { getStatesAndCities, getCitiesByState } from '@/api/statesCities'
import { getCountries } from '@/api/countries'
import { usePhoneMask } from '@/composables/usePhoneMask'
import { useErrorHandler } from '@/composables/useErrorHandler'
import { useGooglePlacesAddress } from '@/composables/useGooglePlacesAddress'

const props = defineProps({
	isMobile: {
		type: Boolean,
		default: false
	},
	showVerification: {
		type: Boolean,
		default: false
	},
	resendCountdown: {
		type: Number,
		default: 0
	},
	verificationMessage: {
		type: String,
		default: ''
	},
	isVerificationLoading: {
		type: Boolean,
		default: false
	}
})

const fieldId = (name) => {
	return props.isMobile ? name : `${name}-inline`
}

const firstName = ref('')
const lastName = ref('')
const email = ref('')
const fullAddress = ref('')
const address = ref('')
const address2 = ref('')
const city = ref('')
const state = ref('')
const zip = ref('')

watch(fullAddress, (val) => {
	if (!zip.value && val) {
		const m = val.match(/\b(\d{5})(?:-\d{4})?/)
		if (m) zip.value = m[1]
	}
})

const { phoneValue, phoneInputRef, setupMask } = usePhoneMask()
const addressInputRef = ref(null)

const { init: initPlacesAddress } = useGooglePlacesAddress({
	inputRef: addressInputRef,
	inputId: props.isMobile ? 'address' : 'address-inline',
	onPlaceSelect: (place, addr) => {
		fullAddress.value = addr.fullAddress || addr.street || ''
		address.value = addr.street || addr.fullAddress || ''
		address2.value = addr.address2 || ''
		city.value = addr.city || ''
		state.value = addr.state || ''
		zip.value = addr.zip || addr.fullAddress?.match(/\b(\d{5})(?:-\d{4})?/)?.[1] || ''
	}
})
const isResendDisabled = computed(() => props.resendCountdown > 0)
const resendCountdown = computed(() => props.resendCountdown)

const verificationCode = ref('')

const states = ref(US_STATES)
const cities = ref([])
const selectedState = ref('')
const selectedCity = ref('')
const cityOther = ref('')
const isLoadingCities = ref(false)

const { handleApiError, handleDataLoadError } = useErrorHandler()

const countries = ref([])
const selectedCountry = ref('USA')
const countryOther = ref('')

const onStateChange = async () => {
	if (!selectedState.value) {
		cities.value = []
		selectedCity.value = ''
		return
	}

	isLoadingCities.value = true
	try {
		const response = await getCitiesByState(selectedState.value)
		if (response.data && Array.isArray(response.data)) {
			cities.value = response.data.sort()
		} else {
			cities.value = []
		}
		selectedCity.value = ''
	} catch (error) {
		handleApiError(error, 'Loading cities')
		cities.value = []
	} finally {
		isLoadingCities.value = false
	}
}

const onCountryChange = () => {
	if (selectedCountry.value !== '__other__') {
		countryOther.value = ''
	}
}

const loadCountries = async () => {
	try {
		const response = await getCountries()
		if (response.data) {
			countries.value = Object.entries(response.data).map(([code, name]) => ({
				code,
				name
			}))
		}
	} catch (error) {
		countries.value = handleDataLoadError(error, 'countries')
	}
}

onMounted(async () => {
	loadCountries()
	await nextTick()
	let attempts = 0
	const trySetupMask = () => {
		if (phoneInputRef.value) {
			setupMask(phoneInputRef.value)
		} else if (attempts < 5) {
			attempts++
			setTimeout(trySetupMask, 100)
		}
	}
	trySetupMask()
	let placesAttempts = 0
	const tryInitPlaces = () => {
		const input = addressInputRef.value || document.getElementById(props.isMobile ? 'address' : 'address-inline')
		if (input && input.isConnected) {
			initPlacesAddress().catch((e) => console.warn('[Places] init error', e))
		} else if (placesAttempts < 25) {
			placesAttempts++
			setTimeout(tryInitPlaces, 200)
		}
	}
	setTimeout(tryInitPlaces, 300)
})

watch([selectedCity, cityOther], () => {
	const cityInput = document.getElementById(fieldId('city'))
	if (cityInput) {
		if (selectedCity.value === '__other__') {
			cityInput.value = cityOther.value
		} else {
			cityInput.value = selectedCity.value
		}
		const label = cityInput.nextElementSibling
		if (label && label.tagName === 'LABEL') {
			const hasValue = (selectedCity.value && selectedCity.value !== '__other__') || 
			                 (selectedCity.value === '__other__' && cityOther.value && cityOther.value.trim() !== '')
			if (hasValue) {
				label.classList.add('hasValue')
			} else {
				label.classList.remove('hasValue')
			}
		}
	}
})

watch([selectedCountry, countryOther], () => {
	if (props.isMobile) return
	const countryInput = document.getElementById(fieldId('country'))
	if (countryInput) {
		if (selectedCountry.value === '__other__') {
			countryInput.value = countryOther.value
		} else {
			countryInput.value = selectedCountry.value
		}
		const label = countryInput.nextElementSibling
		if (label && label.tagName === 'LABEL') {
			const hasValue = (selectedCountry.value && selectedCountry.value !== '__other__') || 
			                 (selectedCountry.value === '__other__' && countryOther.value && countryOther.value.trim() !== '')
			if (hasValue) {
				label.classList.add('hasValue')
			} else {
				label.classList.remove('hasValue')
			}
		}
	}
})

watch(phoneValue, () => {
	const phoneInput = document.getElementById(fieldId('phone'))
	if (phoneInput) {
		const label = phoneInput.nextElementSibling
		if (label && label.tagName === 'LABEL') {
			if (phoneValue.value && phoneValue.value.trim() !== '') {
				label.classList.add('hasValue')
			} else {
				label.classList.remove('hasValue')
			}
		}
	}
})
</script>

<style scoped>
#first-name-inline:placeholder-shown,
#last-name-inline:placeholder-shown,
#email-inline:placeholder-shown,
#phone-inline:placeholder-shown,
#address-inline:placeholder-shown,
#address2-inline:placeholder-shown,
#city-inline:placeholder-shown,
#zip-code-inline:placeholder-shown,
#first-name:placeholder-shown,
#last-name:placeholder-shown,
#email:placeholder-shown,
#phone:placeholder-shown,
#address:placeholder-shown,
#address2:placeholder-shown,
#city:placeholder-shown,
#zip-code:placeholder-shown {
	color: transparent;
}

#phone-inline::placeholder,
#phone::placeholder {
	color: transparent;
	opacity: 0;
}

#phone-verify {
	margin-top: 16px;
}

#success {
	margin-top: 12px;
}

/* Verification Code Styles */
.verification-label {
	display: block;
	margin-bottom: 8px;
	font-weight: 500;
	color: #333;
}

.verification-code-container {
	display: flex;
	gap: 8px;
	align-items: center;
	flex-wrap: wrap;
	margin-top: 8px;
	margin-bottom: 8px;
}

.verification-code-single {
	width: 120px;
	min-width: 120px;
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
	transition: all 0.2s ease;
}

.verification-code-input:focus {
	border-color: var(--primary, #007bff);
	outline: none;
	box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

.verification-code-input:invalid {
	border-color: #dc3545;
}

.resend-code-btn {
	min-width: 120px;
	height: 50px;
	border: 2px solid #ddd;
	border-radius: 8px;
	font-weight: 500;
	transition: all 0.2s ease;
	white-space: nowrap;
}

.resend-code-btn:hover:not(:disabled) {
	background-color: #f8f9fa;
	border-color: #adb5bd;
}

.resend-code-btn:disabled {
	opacity: 0.6;
	cursor: not-allowed;
}

.resend-code-btn:focus {
	outline: none;
	box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
}

@media (max-width: 576px) {
	.verification-code-container {
		flex-direction: column;
		align-items: stretch;
	}
	
	.verification-code-input {
		width: 100%;
	}
	
	.resend-code-btn {
		width: 100%;
	}
}
</style>

