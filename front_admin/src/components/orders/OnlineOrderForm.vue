<template>
	<div class="mt-4">
		<div class="mb-4">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">First Name <span class="text-danger">*</span></label>
					<input
						v-model="form.first_name"
						type="text"
						class="form-control"
						required
						placeholder="Enter first name"
					/>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Last Name <span class="text-danger">*</span></label>
					<input
						v-model="form.last_name"
						type="text"
						class="form-control"
						required
						placeholder="Enter last name"
					/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Email <span class="text-danger">*</span></label>
					<input
						v-model="form.email"
						type="email"
						class="form-control"
						required
						placeholder="Enter email"
					/>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Phone <span class="text-danger">*</span></label>
					<input
						v-model="form.phone"
						@input="formatPhoneNumber"
						type="tel"
						class="form-control"
						required
						placeholder="(XXX) XXX-XXXX"
						maxlength="14"
					/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Address <span class="text-danger">*</span></label>
					<input
						v-model="form.address"
						type="text"
						class="form-control"
						required
						placeholder="Enter address"
					/>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Apartment, Suite</label>
					<input
						v-model="form.address2"
						type="text"
						class="form-control"
						placeholder="Enter apartment, suite"
					/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">State <span class="text-danger">*</span></label>
					<select
						v-model="form.state"
						class="form-control"
						required
						@change="handleStateChange"
					>
						<option value="">Select state</option>
						<option
							v-for="state in onlineStates"
							:key="state.value"
							:value="state.value"
						>
							{{ state.label }}
						</option>
					</select>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">City <span class="text-danger">*</span></label>
					<select
						v-if="form.state && onlineCities.length > 0"
						v-model="form.city"
						class="form-control"
						required
					>
						<option value="">Select city</option>
						<option
							v-for="city in onlineCities"
							:key="city"
							:value="city"
						>
							{{ city }}
						</option>
						<option value="__other__">Other (enter manually)</option>
					</select>
					<input
						v-if="form.city === '__other__'"
						v-model="form.city_other"
						type="text"
						class="form-control mt-2"
						required
						placeholder="Enter city"
					/>
					<input
						v-else-if="!form.state || onlineCities.length === 0"
						v-model="form.city"
						type="text"
						class="form-control"
						required
						placeholder="Enter city"
					/>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Zip Code <span class="text-danger">*</span></label>
					<input
						v-model="form.zip"
						type="text"
						class="form-control"
						required
						placeholder="Enter zip code"
						pattern="[0-9]{5}"
					/>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Country</label>
					<select
						v-model="form.country"
						class="form-control"
						@change="handleCountryChange"
					>
						<option value="">Select country</option>
						<option
							v-for="country in onlineCountries"
							:key="country.code"
							:value="country.code"
						>
							{{ country.name }}
						</option>
						<option value="__other__">Other (enter manually)</option>
					</select>
					<input
						v-if="form.country === '__other__'"
						v-model="form.country_other"
						type="text"
						class="form-control mt-2"
						placeholder="Enter country name"
						maxlength="100"
					/>
				</div>
			</div>
		</div>

		<div class="mt-4">
			<button
				class="btn btn-primary"
				:disabled="!isFormValid || isSubmitting"
				@click="onSubmit"
			>
				<span
					v-if="isSubmitting"
					class="spinner-border spinner-border-sm me-2"
				></span>
				Create Order
			</button>
		</div>
	</div>
</template>

<script setup>
import { computed, watch } from 'vue'

const props = defineProps({
	modelValue: {
		type: Object,
		required: true
	},
	onlineStates: {
		type: Array,
		default: () => []
	},
	onlineCities: {
		type: Array,
		default: () => []
	},
	onlineCountries: {
		type: Array,
		default: () => []
	},
	isSubmitting: {
		type: Boolean,
		default: false
	},
	isFormValid: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['update:modelValue', 'state-change', 'country-change', 'submit'])

const form = computed({
	get: () => props.modelValue,
	set: (value) => emit('update:modelValue', value)
})


const formatPhoneNumber = (event) => {
	let input = event.target
	let value = input.value.replace(/\D/g, '')

	if (value.length >= 10) {
		value = value.substring(0, 10)
	}

	let formatted = ''
	if (value.length >= 6) {
		formatted = `(${value.substring(0, 3)}) ${value.substring(3, 6)}-${value.substring(6)}`
	} else if (value.length >= 3) {
		formatted = `(${value.substring(0, 3)}) ${value.substring(3)}`
	} else if (value.length > 0) {
		formatted = `(${value}`
	}

	if (formatted !== input.value) {
		form.value = {
			...form.value,
			phone: formatted
		}
	}
}

const handleStateChange = (event) => {
	const state = event.target.value
	form.value = {
		...form.value,
		state,
		city: '',
		city_other: ''
	}
	emit('state-change', state)
}

const handleCountryChange = (event) => {
	const country = event.target.value
	form.value = {
		...form.value,
		country,
		country_other: ''
	}
	emit('country-change', country)
}

const onSubmit = () => {
	emit('submit')
}
</script>



