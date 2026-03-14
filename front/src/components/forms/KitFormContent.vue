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
		<div class="control-group form-group">
			<input 
				ref="addressInputRef"
				type="text" 
				class="form-control pac-target-input" 
				:id="fieldId('address')" 
				name="address"
				v-model="address"
				required
				:data-validation-required-message="isMobile ? 'Please enter your address.' : ''"
				placeholder="" 
				autocomplete="off">
			<label :for="fieldId('address')" :class="{ hasValue: address }">Address</label>
		</div>
		<div class="control-group form-group">
			<input 
				type="text" 
				class="form-control" 
				:id="fieldId('address2')" 
				name="address2"
				v-model="address2">
			<label :for="fieldId('address2')" :class="{ hasValue: address2 }">Apartment, Suite</label>
		</div>
		<div class="control-group form-group">
			<select 
				v-model="selectedState" 
				name="state" 
				:id="fieldId('state')" 
				class="form-control"
				required
				@change="onStateChange">
				<option value="" disabled hidden></option>
				<option v-for="s in states" :key="s.value" :value="s.value">{{ s.label }}</option>
			</select>
			<label :for="fieldId('state')" :class="{ hasValue: selectedState }">State</label>
		</div>
		<div class="control-group form-group">
			<select 
				v-if="selectedState && cities.length > 0"
				v-model="selectedCity"
				name="city" 
				:id="fieldId('city')" 
				class="form-control"
				required>
				<option value="">Select city</option>
				<option v-for="city in cities" :key="city" :value="city">{{ city }}</option>
				<option value="__other__">Other (enter manually)</option>
			</select>
			<input 
				v-else
				type="text" 
				class="form-control" 
				:id="fieldId('city')" 
				name="city" 
				v-model="selectedCity"
				required
				:data-validation-required-message="isMobile ? 'Please enter your city.' : ''">
			<input
				v-if="selectedCity === '__other__'"
				v-model="cityOther"
				type="text"
				class="form-control mt-2"
				name="city_other"
				placeholder="Enter city name"
				required>
			<label :for="fieldId('city')" :class="{ hasValue: selectedCity && selectedCity !== '__other__' }">City</label>
		</div>
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
		<div class="row">
			<div class="col-6">
				<div class="control-group form-group">
					<input 
						type="text" 
						:id="fieldId('zip-code')" 
						pattern="[0-9]{5}" 
						class="form-control" 
						name="zip" 
						v-model="zip"
						required
						:data-validation-required-message="isMobile ? 'Please enter your zip code.' : ''">
					<label :for="fieldId('zip-code')" :class="{ hasValue: zip }">Zip Code</label>
				</div>
			</div>
		</div>
		<div id="phone-verify" v-if="showVerification">
			<div class="form-group">
				<label :for="fieldId('verification-code')" class="verification-label">Verification Code from SMS</label>
				<div :id="fieldId('verification-code-container')" class="verification-code-container">
					<input 
						type="text" 
						name="code1" 
						class="form-control verification-code-input" 
						maxlength="1" 
						autocomplete="off"
						inputmode="numeric"
						pattern="[0-9]"
						:ref="el => setCodeInputRef(el, 0)"
						@input="onCodeInput($event, 0)"
						@keydown.backspace="onCodeBackspace($event, 0)"
					>
					<input 
						type="text" 
						name="code2" 
						class="form-control verification-code-input" 
						maxlength="1" 
						autocomplete="off"
						inputmode="numeric"
						pattern="[0-9]"
						:ref="el => setCodeInputRef(el, 1)"
						@input="onCodeInput($event, 1)"
						@keydown.backspace="onCodeBackspace($event, 1)"
					>
					<input 
						type="text" 
						name="code3" 
						class="form-control verification-code-input" 
						maxlength="1" 
						autocomplete="off"
						inputmode="numeric"
						pattern="[0-9]"
						:ref="el => setCodeInputRef(el, 2)"
						@input="onCodeInput($event, 2)"
						@keydown.backspace="onCodeBackspace($event, 2)"
					>
					<input 
						type="text" 
						name="code4" 
						class="form-control verification-code-input" 
						maxlength="1" 
						autocomplete="off"
						inputmode="numeric"
						pattern="[0-9]"
						:ref="el => setCodeInputRef(el, 3)"
						@input="onCodeInput($event, 3)"
						@keydown.backspace="onCodeBackspace($event, 3)"
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
const address = ref('')
const address2 = ref('')
const zip = ref('')

const { phoneValue, phoneInputRef, setupMask } = usePhoneMask()
const addressInputRef = ref(null)

const { init: initPlacesAddress } = useGooglePlacesAddress({
	inputRef: addressInputRef,
	onPlaceSelect: async (place, addr) => {
		address.value = addr.street || addr.fullAddress
		zip.value = addr.zip || ''
		if (addr.state) {
			selectedState.value = addr.state
			await onStateChange()
			selectedCity.value = cities.value.includes(addr.city) ? addr.city : '__other__'
			cityOther.value = cities.value.includes(addr.city) ? '' : (addr.city || '')
		} else {
			selectedCity.value = addr.city ? '__other__' : ''
			cityOther.value = addr.city || ''
		}
	}
})
const isResendDisabled = computed(() => props.resendCountdown > 0)
const resendCountdown = computed(() => props.resendCountdown)

const codeInputs = ref([])

const setCodeInputRef = (el, index) => {
	if (el) {
		codeInputs.value[index] = el
	}
}

const onCodeInput = (event, index) => {
	const input = event.target
	const value = input.value.replace(/\D/g, '')
	input.value = value.slice(0, 1)

	if (value && index < codeInputs.value.length - 1) {
		const next = codeInputs.value[index + 1]
		if (next) {
			next.focus()
			next.select()
		}
	}
}

const onCodeBackspace = (event, index) => {
	const input = event.target
	if (event.key === 'Backspace' && !input.value && index > 0) {
		const prev = codeInputs.value[index - 1]
		if (prev) {
			prev.focus()
			prev.select()
		}
	}
}

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
		if (addressInputRef.value) {
			initPlacesAddress()
		} else if (placesAttempts < 10) {
			placesAttempts++
			setTimeout(tryInitPlaces, 100)
		}
	}
	tryInitPlaces()
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

