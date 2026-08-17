<template>
	<div class="offline-order-form">
		<div class="order-form-edit">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Branch <span class="text-danger">*</span></label>
					<select v-model="formData.branch_id" class="form-control" required>
						<option v-for="branch in branches" :key="branch.id" :value="branch.id">
							{{ branch.display_name || branch.name }}
						</option>
					</select>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Name <span class="text-danger">*</span></label>
					<input v-model="formData.name" type="text" class="form-control" required
						placeholder="Enter full name" :disabled="!!props.selectedUser" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Email</label>
					<input v-model="formData.email" type="email" class="form-control"
						placeholder="Enter email (optional)" :disabled="!!props.selectedUser" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Phone <span class="text-danger">*</span></label>
					<input v-model="formData.phone" @input="formatPhoneNumber" type="tel" class="form-control" required
						placeholder="(XXX) XXX-XXXX" maxlength="14" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Birth Date</label>
					<input v-model="formData.date_of_birth" type="date" class="form-control" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Government ID</label>
					<input v-model="formData.government_id_number" type="text" class="form-control"
						placeholder="Gov. ID #" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">State Issued</label>
					<input v-model="formData.state_issued" type="text" class="form-control"
						placeholder="State that issued ID" maxlength="100" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 mb-3">
					<label class="form-label">Address</label>
					<input v-model="formData.address" type="text" class="form-control" placeholder="Enter address" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">State/Province</label>
					<select v-model="formData.state" class="form-control" @change="onStateChange">
						<option value="">Select state/province</option>
						<option v-for="state in states" :key="state.value" :value="state.value">
							{{ state.label }}
						</option>
						<option value="__other__">Other (enter manually)</option>
					</select>
					<input v-if="formData.state === '__other__'" v-model="formData.state_other" type="text" :class="[
						'form-control mt-2',
						formData.state === '__other__' && formData.state_other && formData.state_other.length !== 2 ? 'is-invalid' : ''
					]" placeholder="Enter 2-letter state abbreviation (e.g., CA, NY)" maxlength="2"
						style="text-transform: uppercase;"
						@input="formData.state = formData.state_other.toUpperCase()" />
					<div v-if="formData.state === '__other__' && formData.state_other && formData.state_other.length !== 2"
						class="invalid-feedback">
						State abbreviation must be exactly 2 letters.
					</div>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">City</label>
					<input
						v-model="formData.city"
						type="text"
						class="form-control"
						placeholder="Enter city"
						list="offline-city-suggestions"
					/>
					<datalist id="offline-city-suggestions">
						<option v-for="city in cities" :key="city" :value="city" />
					</datalist>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Postal Code</label>
					<input v-model="formData.postal_code" type="text" class="form-control"
						placeholder="Enter postal code" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Country</label>
					<select v-model="formData.country" class="form-control" @change="onCountryChange">
						<option value="">Select country</option>
						<option v-for="country in countries" :key="country.code" :value="country.code">
							{{ country.name }}
						</option>
						<option value="__other__">Other (enter manually)</option>
					</select>
					<input v-if="formData.country === '__other__'" v-model="formData.country_other" type="text"
						class="form-control mt-2" placeholder="Enter country name" maxlength="100" />
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label">Transaction Amount <span class="text-danger">*</span></label>
					<input v-model="formData.amount" type="number" class="form-control" step="0.01" min="0" required
						placeholder="0.00" />
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label">Payment Method</label>
					<select v-model="formData.payment_method" class="form-control">
						<option
							v-for="opt in paymentMethodOptions"
							:key="opt.value"
							:value="opt.value"
						>
							{{ opt.label }}
						</option>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 mb-3">
					<label class="form-label">Items Description</label>
					<div v-for="(item, index) in formData.items_description" :key="index" class="d-flex mb-2">
						<input
							v-model="formData.items_description[index]"
							type="text"
							class="form-control me-2"
							:placeholder="`Item ${index + 1}`"
						/>
						<button
							type="button"
							class="btn btn-sm btn-outline-danger"
							@click="removeItem(index)"
						>
							<iconify-icon icon="solar:trash-bin-minimalistic-outline" />
						</button>
					</div>
					<button
						type="button"
						class="btn btn-sm btn-outline-primary"
						:disabled="formData.items_description.length >= 10"
						@click="addItem"
					>
						<iconify-icon icon="solar:add-circle-outline" class="me-1" />
						Add Item
					</button>
					<small class="form-text text-muted d-block mt-1">Up to 10 items for the Information Card PDF</small>
				</div>
			</div>

			<div class="d-flex gap-3 mt-4">
				<button class="btn btn-primary" :disabled="!isFormValid || isSubmitting" @click="handleSubmit(false)">
					<span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
					Create Order
				</button>
				<button class="btn btn-outline-secondary" :disabled="!isFormValid || isSubmitting"
					@click="handleSubmit(true)">
					<span v-if="isSubmitting" class="spinner-border spinner-border-sm me-2"></span>
					Create & Download PDF
				</button>
			</div>
		</div>

	</div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { getStatesAndCities, getCitiesByState } from '@/api/adminStatesCities'
import { getCountries } from '@/api/adminCountries'
import { US_STATES_CODE_NAME } from '@/constants/states'

const props = defineProps({
	branches: {
		type: Array,
		default: () => []
	},
	isSubmitting: {
		type: Boolean,
		default: false
	},
	selectedUser: {
		type: Object,
		default: null
	}
})

const emit = defineEmits(['submit'])

const formData = ref({
	user_id: null,
	branch_id: null,
	name: '',
	email: '',
	phone: '',
	date_of_birth: '',
	government_id_number: '',
	state_issued: '',
	address: '',
	city: '',
	state: '',
	state_other: '',
	postal_code: '',
	country: '',
	country_other: '',
	amount: null,
	payment_method: '',
	items_description: []
})

const paymentMethodOptions = [
	{ value: '', label: 'Select payment method' },
	{ value: 'cash', label: 'Cash' },
	{ value: 'card', label: 'Card' },
	{ value: 'check', label: 'Check' },
	{ value: 'other', label: 'Other' }
]

const states = ref([])
const cities = ref([])
const countries = ref([])
const isLoadingCities = ref(false)

const resolveStateCode = (user) => {
	if (user.state_code && user.state_code.length === 2) {
		return user.state_code.toUpperCase()
	}
	const rawState = user.state || ''
	if (rawState.length === 2) {
		return rawState.toUpperCase()
	}
	const match = US_STATES_CODE_NAME.find(s => s.name === rawState)
	return match ? match.code : rawState
}

const getStateNameByCode = (stateCode) => {
	const match = states.value.find(s => s.value === stateCode)
	return match ? match.label : stateCode
}

const loadCitiesForState = async (stateCode, savedCity = '') => {
	if (!stateCode || stateCode === '__other__') {
		cities.value = []
		return
	}

	const stateName = getStateNameByCode(stateCode)
	isLoadingCities.value = true
	try {
		const response = await getCitiesByState(stateName)
		if (response.data && Array.isArray(response.data)) {
			cities.value = response.data.sort()
		} else {
			cities.value = []
		}
		if (savedCity) {
			formData.value.city = savedCity
		}
	} catch (error) {
		console.error('Failed to load cities:', error)
		cities.value = []
		if (savedCity) {
			formData.value.city = savedCity
		}
	} finally {
		isLoadingCities.value = false
	}
}

const loadStatesAndCities = async () => {
	try {
		const response = await getStatesAndCities()
		if (response.data) {
			const stateAbbreviations = Object.fromEntries(US_STATES_CODE_NAME.map(s => [s.name, s.code]))

			states.value = Object.keys(response.data).map(fullName => ({
				value: stateAbbreviations[fullName] || fullName.substring(0, 2).toUpperCase(),
				label: fullName
			})).sort((a, b) => a.label.localeCompare(b.label))
		}
	} catch (error) {
		console.error('Failed to load states:', error)
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
		console.error('Failed to load countries:', error)
		countries.value = [
			{ code: 'USA', name: 'United States' },
			{ code: 'CA', name: 'Canada' },
			{ code: 'MX', name: 'Mexico' },
		]
	}
}

const onStateChange = async () => {
	if (!formData.value.state || formData.value.state === '__other__') {
		cities.value = []
		formData.value.city = ''
		return
	}

	await loadCitiesForState(formData.value.state)
	formData.value.city = ''
}

const onCountryChange = () => {
	if (formData.value.country !== '__other__') {
		formData.value.country_other = ''
	}
}

const getCountryValue = () => {
	return formData.value.country === '__other__'
		? formData.value.country_other
		: formData.value.country
}

const getStateValue = () => {
	if (formData.value.state === '__other__') {
		return formData.value.state_other?.toUpperCase() || ''
	}
	return formData.value.state || ''
}

const addItem = () => {
	if (formData.value.items_description.length < 10) {
		formData.value.items_description.push('')
	}
}

const removeItem = (index) => {
	formData.value.items_description.splice(index, 1)
}

const prefillFromUser = async (user) => {
	if (!user) return

	const savedCity = user.city || ''
	const stateCode = resolveStateCode(user)

	formData.value.user_id = user.id
	formData.value.name = user.name || ''
	formData.value.email = user.email || ''
	formData.value.phone = user.phone || ''
	formData.value.date_of_birth = user.date_of_birth || ''
	formData.value.government_id_number = user.government_id_number || ''
	formData.value.state_issued = user.state_issued || ''
	formData.value.address = user.address || ''
	formData.value.postal_code = user.zip || user.postal_code || ''
	formData.value.country = user.country || 'USA'
	formData.value.state = stateCode
	formData.value.state_other = ''
	formData.value.amount = null
	formData.value.items_description = []

	if (stateCode && stateCode !== '__other__' && stateCode.length === 2) {
		await loadCitiesForState(stateCode, savedCity)
	} else {
		formData.value.city = savedCity
	}
}

watch(() => props.selectedUser, async (newUser) => {
	if (newUser) {
		await prefillFromUser(newUser)
	}
}, { immediate: true })

watch(() => props.branches, (newBranches) => {
	if (newBranches && newBranches.length > 0 && !formData.value.branch_id) {
		formData.value.branch_id = newBranches[0].id
	}
}, { immediate: true })

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
		formData.value.phone = formatted
	}
}

onMounted(async () => {
	await loadStatesAndCities()
	await loadCountries()
	if (!formData.value.country) {
		formData.value.country = 'USA'
	}
	if (props.selectedUser) {
		await prefillFromUser(props.selectedUser)
	}
})

const isFormValid = computed(() => {
	let stateValid = true
	if (formData.value.state === '__other__') {
		stateValid = formData.value.state_other && formData.value.state_other.length === 2
	} else if (formData.value.state) {
		stateValid = formData.value.state.length === 2
	}

	return !!(
		formData.value.branch_id &&
		(props.selectedUser || (formData.value.name && formData.value.phone)) &&
		formData.value.amount && formData.value.amount > 0 &&
		stateValid
	)
})

const handleSubmit = (printAfterCreate) => {
	if (!isFormValid.value) return

	const cleanedItems = formData.value.items_description
		.map(item => item.trim())
		.filter(item => item !== '')

	const submitData = {
		...formData.value,
		state: getStateValue(),
		country: getCountryValue(),
		items_description: cleanedItems,
		printAfterCreate
	}

	delete submitData.state_other
	delete submitData.country_other

	emit('submit', submitData)
}

</script>

<style scoped>
.offline-order-form {
	width: 100%;
}

.order-form-print {
	position: fixed;
	left: 0;
	top: 0;
	width: 100%;
	height: 100%;
	background: white;
	z-index: 9999;
	overflow-y: auto;
}

@media screen {

	.print-container {
		width: 100%;
		max-width: 800px;
		margin: 0 auto;
		padding: 20px;
		background: white;
	}

	.print-header {
		border-bottom: 2px solid #000;
		padding-bottom: 10px;
		margin-bottom: 20px;
	}

	.print-header h4 {
		font-size: 24px;
		font-weight: bold;
		margin: 0;
	}

	.print-section {
		margin-bottom: 20px;
	}

	.print-section h6 {
		font-weight: bold;
		font-size: 16px;
		margin-bottom: 10px;
		border-bottom: 1px solid #ccc;
		padding-bottom: 5px;
	}

	.print-table {
		width: 100%;
		border-collapse: collapse;
		margin-bottom: 10px;
	}

	.print-table tr {
		border-bottom: 1px solid #ddd;
	}

	.print-table tr:last-child {
		border-bottom: none;
	}

	.print-table td {
		padding: 8px 5px;
		vertical-align: top;
	}

	.print-label {
		font-weight: bold;
		width: 35%;
		font-size: 11pt;
		color: #333;
	}

	.print-value {
		font-size: 11pt;
		color: #000;
		word-wrap: break-word;
	}
}
</style>

<style>
@media print {

	body * {
		visibility: hidden;
	}

	.offline-order-form,
	.offline-order-form * {
		visibility: visible !important;
	}

	.order-form-print,
	.order-form-print * {
		visibility: visible !important;
	}

	.order-form-print {
		position: fixed !important;
		left: 0 !important;
		top: 0 !important;
		width: 100% !important;
		height: 100% !important;
		display: block !important;
		visibility: visible !important;
		background: white !important;
		z-index: 9999 !important;
		overflow: visible !important;
	}

	.print-container {
		visibility: visible !important;
		display: block !important;
	}

	.print-header,
	.print-body,
	.print-section,
	.print-row,
	.print-label,
	.print-value {
		visibility: visible !important;
		display: block !important;
	}

	.print-row {
		display: flex !important;
	}

	.order-form-edit {
		display: none !important;
		visibility: hidden !important;
	}

	body>*:not(.offline-order-form) {
		display: none !important;
		visibility: hidden !important;
	}

	body>*:not(.offline-order-form) * {
		display: none !important;
		visibility: hidden !important;
	}

	.offline-order-form {
		display: block !important;
		visibility: visible !important;
		position: fixed;
		left: 0;
		top: 0;
		width: 100%;
		height: 100%;
		background: white;
		z-index: 9999;
	}

	.offline-order-form {
		display: block !important;
		visibility: visible !important;
	}

}

.cursor-pointer {
	cursor: pointer;
}

.cursor-pointer:hover {
	background-color: #f8f9fa;
}
</style>
