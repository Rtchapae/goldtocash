<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Add Transaction" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12">
					<div class="card">
						<div class="card-body">
							<OrderUserSearch
								v-model="userSearchQuery"
								:results="userSearchResults"
								@search="searchUser"
								@select="selectUser"
							/>

							<div class="mb-4">
								<label class="form-label fw-semibold mb-3">Order Type</label>
								<div class="d-flex gap-3">
									<button
										:class="['btn', orderType === ORDER_TYPE_ONLINE ? 'btn-primary' : 'btn-outline-primary']"
										@click="orderType = ORDER_TYPE_ONLINE"
									>
										Online
									</button>
									<button
										:class="['btn', orderType === ORDER_TYPE_OFFLINE ? 'btn-primary' : 'btn-outline-primary']"
										@click="orderType = ORDER_TYPE_OFFLINE"
									>
										Offline
									</button>
								</div>
							</div>

							<OnlineOrderForm
								v-if="orderType === ORDER_TYPE_ONLINE"
								v-model="newUserData"
								:online-states="onlineStates"
								:online-cities="onlineCities"
								:online-countries="onlineCountries"
								:is-submitting="isSubmitting"
								:is-form-valid="isOnlineFormValid"
								@state-change="onStateChange"
								@country-change="onCountryChange"
								@submit="createOnlineOrder"
							/>
							
							<OfflineOrderForm
								v-if="orderType === ORDER_TYPE_OFFLINE"
								:branches="branches"
								:is-submitting="isSubmitting"
								:selected-user="selectedUser"
								@submit="createOfflineOrder"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import OfflineOrderForm from '@/components/orders/OfflineOrderForm.vue'
import OrderUserSearch from '@/components/orders/OrderUserSearch.vue'
import OnlineOrderForm from '@/components/orders/OnlineOrderForm.vue'
import { searchUserByEmailOrPhone, createOrder, downloadOrderPdf as downloadOrderPdfApi } from '@/api/adminOrders'
import { getBranches } from '@/api/adminBranches'
import { getCitiesByState } from '@/api/adminStatesCities'
import { getCountries } from '@/api/adminCountries'
import { useToast } from '@/composables/useToast'
import { US_STATES } from '@/constants/states'

const ORDER_TYPE_ONLINE = 'online'
const ORDER_TYPE_OFFLINE = 'offline'
const COUNTRY_CODE_USA = 'USA'
const COUNTRY_CODE_CANADA = 'CA'
const COUNTRY_CODE_MEXICO = 'MX'
const COUNTRY_OTHER_VALUE = '__other__'

const router = useRouter()
const toast = useToast()

const orderType = ref('')
const userSearchQuery = ref('')
const userSearchResults = ref([])
const selectedUser = ref(null)
const branches = ref([])
const isLoading = ref(false)
const isSubmitting = ref(false)
const onlineStates = US_STATES
const onlineCities = ref([])
const onlineCountries = ref([])
const isLoadingCities = ref(false)

const createEmptyNewUserData = () => ({
	first_name: '',
	last_name: '',
	email: '',
	phone: '',
	address: '',
	address2: '',
	city: '',
	city_other: '',
	state: '',
	zip: '',
	country: COUNTRY_CODE_USA,
	country_other: ''
})

const newUserData = ref(createEmptyNewUserData())

const searchUser = async () => {
	if (!userSearchQuery.value || userSearchQuery.value.length < 3) {
		userSearchResults.value = []
		return
	}

	try {
		const response = await searchUserByEmailOrPhone(userSearchQuery.value)
		userSearchResults.value = response.data || []
	} catch (error) {
		console.error('Failed to search user:', error)
		userSearchResults.value = []
	}
}

const selectUser = async (user) => {
	selectedUser.value = user
	userSearchResults.value = []
	userSearchQuery.value = user.email || user.phone || ''
	
	const nameParts = (user.name || '').split(' ')
	newUserData.value.first_name = user.first_name || nameParts[0] || ''
	newUserData.value.last_name = user.last_name || (nameParts.length > 1 ? nameParts.slice(1).join(' ') : '') || ''
	newUserData.value.email = user.email || ''
	const phoneValue = user.phone || ''
	newUserData.value.phone = phoneValue
	newUserData.value.address = user.address || ''
	newUserData.value.address2 = user.address2 || ''
	newUserData.value.city = user.city || ''
	newUserData.value.city_other = ''
	
	let stateValue = user.state || ''
	if (stateValue && stateValue.length > 2) {
		const stateObj = onlineStates.find(s => s.label === stateValue)
		if (stateObj) {
			stateValue = stateObj.value
		}
	}
	newUserData.value.state = stateValue
	newUserData.value.zip = user.zip || ''
	const countryValue = user.country || COUNTRY_CODE_USA
	newUserData.value.country = countryValue
	newUserData.value.country_other = ''
	
	if (newUserData.value.state && newUserData.value.state !== '__other__') {
		await onStateChange()
		if (user.city) {
			newUserData.value.city = user.city
		}
	}
}

const clearSelectedUser = () => {
	selectedUser.value = null
	userSearchQuery.value = ''
	userSearchResults.value = []
	onlineCities.value = []
	newUserData.value = createEmptyNewUserData()
}

const onStateChange = async () => {
	if (!newUserData.value.state) {
		onlineCities.value = []
		newUserData.value.city = ''
		newUserData.value.city_other = ''
		return
	}

	isLoadingCities.value = true
	try {
		const response = await getCitiesByState(newUserData.value.state)
		console.log('Cities API response:', response)
		
		if (response.data && Array.isArray(response.data)) {
			onlineCities.value = response.data.sort()
		} else {
			onlineCities.value = []
		}
		newUserData.value.city = ''
		newUserData.value.city_other = ''
	} catch (error) {
		console.error('Failed to load cities:', error)
		onlineCities.value = []
	} finally {
		isLoadingCities.value = false
	}
}

const onCountryChange = () => {
	if (newUserData.value.country !== COUNTRY_OTHER_VALUE) {
		newUserData.value.country_other = ''
	}
}

const getCountryValue = () => {
	return newUserData.value.country === COUNTRY_OTHER_VALUE 
		? newUserData.value.country_other 
		: newUserData.value.country
}

const isOnlineFormValid = computed(() => {
	const cityValue = newUserData.value.city === '__other__' 
		? newUserData.value.city_other 
		: newUserData.value.city
	const isValidCity = cityValue && cityValue.trim() !== '' && cityValue !== '__other__'
	
	if (selectedUser.value) {
		return !!(newUserData.value.address && isValidCity && newUserData.value.state && newUserData.value.zip)
	}
	return !!(
		newUserData.value.first_name &&
		newUserData.value.last_name &&
		newUserData.value.email &&
		newUserData.value.phone &&
		newUserData.value.address &&
		isValidCity &&
		newUserData.value.state &&
		newUserData.value.zip
	)
})

const createOnlineOrder = async () => {
	if (!isOnlineFormValid.value) {
		toast.error('Please select a user or enter user information')
		return
	}

	isSubmitting.value = true
	try {
		let userId = null

		if (selectedUser.value) {
			userId = selectedUser.value.id
		} else {
			if (newUserData.value.email || newUserData.value.phone) {
				try {
					const searchResponse = await searchUserByEmailOrPhone(newUserData.value.email || newUserData.value.phone)
					if (searchResponse.data && searchResponse.data.length > 0) {
						userId = searchResponse.data[0].id
					}
				} catch (error) {
					console.warn('User search failed:', error)
				}
			}

			if (!userId) {
				const cityValue = newUserData.value.city === COUNTRY_OTHER_VALUE 
					? newUserData.value.city_other 
					: newUserData.value.city
					
				const userResponse = await createUser({
					first_name: newUserData.value.first_name,
					last_name: newUserData.value.last_name,
					name: `${newUserData.value.first_name} ${newUserData.value.last_name}`,
					email: newUserData.value.email,
					phone: newUserData.value.phone,
					address: newUserData.value.address,
					address2: newUserData.value.address2 || null,
					city: cityValue,
					state: newUserData.value.state,
					zip: newUserData.value.zip,
					country: getCountryValue() || COUNTRY_CODE_USA
				})
				userId = userResponse.data?.id || userResponse.data?.user?.id
			}
		}

		const response = await createOrder({
			user_id: userId,
			order_type: ORDER_TYPE_ONLINE
		})

		if (response.status) {
			toast.success('Order created successfully')
			router.push({ name: 'orders.list' })
		}
	} catch (error) {
		toast.error(error.data?.message || error.message || 'Failed to create order')
	} finally {
		isSubmitting.value = false
	}
}

const createOfflineOrder = async (formData) => {
	isSubmitting.value = true
	try {
		let orderData = {
			order_type: ORDER_TYPE_OFFLINE,
			branch_id: formData.branch_id,
			amount: formData.amount,
			payment_method: formData.payment_method,
			notes: formData.notes || null
		}

		if (!formData.user_id) {
			const countryValue = formData.country === COUNTRY_OTHER_VALUE
				? formData.country_other
				: formData.country

			const cityValue = formData.city === '__other__'
				? formData.city_other
				: formData.city

			orderData.user_data = {
				first_name: formData.name?.split(' ')[0] || formData.name,
				last_name: formData.name?.split(' ').slice(1).join(' ') || '',
				name: formData.name,
				email: formData.email,
				phone: formData.phone,
				address: formData.address || '',
				city: cityValue || '',
				state: formData.state || '',
				zip: formData.postal_code || '',
				country: countryValue || COUNTRY_CODE_USA
			}
		} else {
			orderData.user_id = formData.user_id
		}

		const response = await createOrder(orderData)

		if (response.status) {
			toast.success('Order created successfully')
			if (formData.printAfterCreate) {
				await downloadOrderPdf(response.order_id || response.data?.id)
			}
			router.push({ name: 'orders.list' })
		}
	} catch (error) {
		toast.error(error.data?.message || error.message || 'Failed to create order')
	} finally {
		isSubmitting.value = false
	}
}

const downloadOrderPdf = async (orderId) => {
	try {
		const blob = await downloadOrderPdfApi(orderId)
		const url = window.URL.createObjectURL(blob)

		const link = document.createElement('a')
		link.href = url
		link.download = `offline_order_${orderId}.pdf`
		link.style.display = 'none'

		document.body.appendChild(link)
		link.click()
		document.body.removeChild(link)

		window.URL.revokeObjectURL(url)

		toast.success('PDF downloaded successfully')
	} catch (error) {
		console.error('Failed to download PDF:', error)
		toast.error('Failed to download PDF')
	}
}

const loadBranches = async () => {
	isLoading.value = true
	try {
		const response = await getBranches()
		console.log('Branches API response:', response)
		
		if (response && response.data && Array.isArray(response.data)) {
			branches.value = response.data
		} else if (Array.isArray(response)) {
			branches.value = response
		} else {
			branches.value = []
		}
		
		console.log('Loaded branches:', branches.value)
		
		if (branches.value.length === 0) {
			console.warn('No branches loaded')
		}
	} catch (error) {
		console.error('Failed to load branches:', error)
		toast.error('Failed to load branches: ' + (error.message || 'Unknown error'))
		branches.value = []
	} finally {
		isLoading.value = false
	}
}

watch(orderType, (newType) => {
	if (newType === ORDER_TYPE_OFFLINE && branches.value.length === 0) {
		loadBranches()
	}
})

const loadCountries = async () => {
	try {
		const response = await getCountries()
		if (response.data) {	
			onlineCountries.value = Object.entries(response.data).map(([code, name]) => ({
				code,
				name
			}))
		}
	} catch (error) {
		console.error('Failed to load countries:', error)
		onlineCountries.value = [
			{ code: COUNTRY_CODE_USA, name: 'United States' },
			{ code: COUNTRY_CODE_CANADA, name: 'Canada' },
			{ code: COUNTRY_CODE_MEXICO, name: 'Mexico' },
		]
	}
}

onMounted(() => {
	loadCountries()
	if (orderType.value === ORDER_TYPE_OFFLINE) {
		loadBranches()
	}
})
</script>

