<template>
	<section class="section-user_account content">
		<div class="m-3">
				<div class="my-5 row">
					<div class="col-lg-12">
						<OrderStatusTimeline :current-status-index="currentStatusIndex" :has-orders="hasOrders" />
					</div>
				</div>
				<div v-if="offerOrder" class="card alert alert-success offer-panel mt-3 mb-4" role="alert">
					<div class="card-body">
						<div class="row align-items-center">
							<div class="col-12 col-md-4 text-center mb-3 mb-md-0">
								<div class="offer-amount">{{ formatAmount(offerOrder.amount) }}</div>
								<p class="offer-order-id mb-0">Order #{{ offerOrder.id }}</p>
							</div>
							<div class="col-12 col-md-5">
								<p class="offer-title mb-2">Congratulations! We have an offer for you!</p>
								<p class="offer-desc mb-1">We will initiate a payment as soon as you accept our offer.
								</p>
								<p class="offer-desc-small mb-0">
									Not satisfied with the offer or have questions? Call us at
									<a href="tel:5642377332" class="text-dark">(564) 237-7332</a>
									or <router-link to="/contact-us" class="text-dark">contact us</router-link>.
								</p>
							</div>
							<div class="col-12 col-md-3 text-center mt-3 mt-md-0">
								<div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
									<button type="button" class="btn btn-light btn-lg" :disabled="offerResponding"
										@click="submitOfferResponse(offerOrder.id, 'accept')">
										<span v-if="offerResponding === 'accept'"
											class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
										Accept
									</button>
									<button type="button" class="btn btn-danger btn-lg" :disabled="offerResponding"
										@click="submitOfferResponse(offerOrder.id, 'deny')">
										<span v-if="offerResponding === 'deny'"
											class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
										Decline
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<div class="row g-3">
							<div class="col-12 col-lg-6">
								<OrdersCard :orders="orders" :format-date="formatDate" :format-amount="formatAmount"
									:get-status-label="getStatusLabel" @request-kit="openCreateNewRequest" />
							</div>

							<div class="col-12 col-lg-6">
								<ProfileCard :user-profile="userProfile" :format-phone="formatPhone"
									@edit="openEditProfile" />
							</div>

							<div class="col-12 col-lg-6">
								<VerificationCard :user-profile="userProfile"
									:is-verification-complete="isVerificationComplete" :format-date="formatDate"
									@edit-payment-method="openEditPaymentMethod" @edit-docs="openEditDocs"
									@edit-date-of-birth="openEditDateOfBirth" />
							</div>

							<div class="col-12 col-lg-6">
								<DocumentsCard :documents="documents" :is-loading="isLoading"
									@upload="openUploadDocument" />
							</div>
						</div>
					</div>
				</div>
			</div>

		<EditUserProfileModal :open="isEditProfileOpen" :user="userProfile" :orders-count="orders.length"
			:is-loading="isEditProfileLoading" @close="closeEditProfile" @submit="submitEditProfile" />

		<EditBankSettingsModal :open="isEditPaymentMethodOpen" :initial="paymentMethodData"
			:is-loading="isEditPaymentMethodLoading" @close="closeEditPaymentMethod"
			@submit="submitEditPaymentMethod" />

		<EditDocsSettingsModal :open="isEditDocsOpen" :initial="governmentIdData" :is-loading="isEditDocsLoading"
			@close="closeEditDocs" @submit="submitEditDocs" />

		<EditDateSettingsModal :open="isEditDateOfBirthOpen" :date-of-birth="userProfile?.date_of_birth || ''"
			:is-loading="isEditDateOfBirthLoading" @close="closeEditDateOfBirth" @submit="submitEditDateOfBirth" />

		<UploadDocumentModal :open="isUploadDocumentOpen" :is-loading="isUploadDocumentLoading"
			@close="closeUploadDocument" @submit="submitUploadDocument" />

		<CreateNewRequestModal :open="isCreateNewRequestOpen" :is-loading="isCreateNewRequestLoading"
			@close="closeCreateNewRequest" @confirm="submitCreateNewRequest" />
	</section>
</template>

<script setup>
import { ref, computed, onMounted, inject } from 'vue'
import { useRouter } from 'vue-router'
import { getUserOrders, getUserDocuments, updateUserProfile, uploadDocument, createKitRequest, respondToOffer } from '@/api/userAccount'
import { useUserStore } from '@/stores/user'
import { useToast } from '@/composables/useToast'
import OrderStatusTimeline from '@/components/userAccount/OrderStatusTimeline.vue'
import OrdersCard from '@/components/userAccount/OrdersCard.vue'
import ProfileCard from '@/components/userAccount/ProfileCard.vue'
import VerificationCard from '@/components/userAccount/VerificationCard.vue'
import DocumentsCard from '@/components/userAccount/DocumentsCard.vue'
import EditUserProfileModal from '@/components/userAccount/modals/EditUserProfileModal.vue'
import EditBankSettingsModal from '@/components/userAccount/modals/EditBankSettingsModal.vue'
import EditDocsSettingsModal from '@/components/userAccount/modals/EditDocsSettingsModal.vue'
import EditDateSettingsModal from '@/components/userAccount/modals/EditDateSettingsModal.vue'
import UploadDocumentModal from '@/components/userAccount/modals/UploadDocumentModal.vue'
import CreateNewRequestModal from '@/components/userAccount/modals/CreateNewRequestModal.vue'
import { formatDateMMDDYYYY, formatAmount2dp, formatPhoneUS } from '@/utils/formatters'

const router = useRouter()
const userStore = useUserStore()
const toast = useToast()

const userProfile = computed(() => userStore.profile)
const orders = ref([])
const documents = ref([])
const isLoading = ref(false)

const statusLabels = [
	'Kit Requested',
	'In Transit',
	'Items Received',
	'Appraisal',
	'Offer Sent',
	'Offer Accepted',
	'Paid',
	'Offer Denied',
	'Items Sent Back',
	'No Sale',
	'Items Sent Back',
]

const hasOrders = computed(() => orders.value.length > 0)

// Order with status Offer Sent (4) — show accept/deny panel
const OFFER_SENT_STATUS = 4
const offerOrder = computed(() => {
	if (!orders.value.length) return null
	const last = orders.value[0]
	return Number(last.status) === OFFER_SENT_STATUS ? last : null
})

const offerResponding = ref(null)

const isEditProfileOpen = ref(false)
const isEditPaymentMethodOpen = ref(false)
const isEditDocsOpen = ref(false)
const isEditDateOfBirthOpen = ref(false)
const isUploadDocumentOpen = ref(false)
const isCreateNewRequestOpen = ref(false)

const isEditProfileLoading = ref(false)
const isEditPaymentMethodLoading = ref(false)
const isEditDocsLoading = ref(false)
const isEditDateOfBirthLoading = ref(false)
const isUploadDocumentLoading = ref(false)
const isCreateNewRequestLoading = ref(false)

const currentStatusIndex = computed(() => {
	// If there are no orders yet, don't highlight any status (all inactive/grey)
	if (!hasOrders.value) return -1
	const lastOrder = orders.value[0]
	const status = Number(lastOrder.status)
	return Number.isFinite(status) ? status : 0
})

const isVerificationComplete = computed(() => {
	return userProfile.value?.payment_method &&
		userProfile.value?.government_id &&
		userProfile.value?.date_of_birth
})

const getStatusLabel = (status) => {
	return statusLabels[status] || 'Unknown'
}

const formatDate = formatDateMMDDYYYY
const formatAmount = formatAmount2dp
const formatPhone = formatPhoneUS

const loadUserData = async () => {
	isLoading.value = true
	try {
		const [, ordersData, documentsData] = await Promise.all([
			userStore.loadProfile(),
			getUserOrders(),
			getUserDocuments(),
		])

		orders.value = ordersData.orders || ordersData || []

		// Load documents from API (user-uploaded files)
		const userDocuments = documentsData.documents || documentsData || []

		// Also load documents from orders (if any)
		const orderDocuments = orders.value
			.filter(order => order.documents && order.documents.length > 0)
			.flatMap(order => order.documents)

		// Combine both sources (user documents + order documents)
		documents.value = [...userDocuments, ...orderDocuments]
	} catch (error) {
		console.error('Failed to load user data:', error)
		// Redirect to login if unauthorized
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			toast.error(error?.message || error?.data?.message || 'Failed to load user data. Please try again.')
		}
	} finally {
		isLoading.value = false
	}
}

const openEditProfile = () => {
	isEditProfileOpen.value = true
}

const closeEditProfile = () => {
	isEditProfileOpen.value = false
}

const submitEditProfile = async (payload) => {
	// wired: persist to backend and update store profile (no frontend mapping)
	isEditProfileLoading.value = true
	try {
		const updated = await updateUserProfile(payload)
		userStore.setProfile(updated)
		toast.success('Profile updated successfully!')
		closeEditProfile()
	} catch (error) {
		console.error('Failed to update profile:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to update profile. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isEditProfileLoading.value = false
	}
}

const paymentMethodData = computed(() => {
	// Extract payment method data from user profile
	// Backend should return payment_method as string and payment_method_params as object
	const profile = userProfile.value
	if (!profile) return null

	// Parse payment_method_params if it's a string, otherwise use as-is
	let params = {}
	if (profile.payment_method_params) {
		if (typeof profile.payment_method_params === 'string') {
			try {
				params = JSON.parse(profile.payment_method_params)
			} catch {
				params = {}
			}
		} else {
			params = profile.payment_method_params
		}
	}

	return {
		type: profile.payment_method || 'check',
		checkName: params.checkName || params.check_name || '',
		checkAddress: params.checkAddress || params.check_address || '',
		achRouting: params.achRouting || params.ach_routing || '',
		achAccount: params.achAccount || params.ach_account || '',
		paypalEmail: params.paypalEmail || params.paypal_email || '',
		cashTag: params.cashTag || params.cash_tag || '',
	}
})

const openEditPaymentMethod = () => {
	isEditPaymentMethodOpen.value = true
}

const closeEditPaymentMethod = () => {
	isEditPaymentMethodOpen.value = false
}

const submitEditPaymentMethod = async (payload) => {
	isEditPaymentMethodLoading.value = true
	try {
		// Prepare data for backend (no frontend mapping - backend expects exact format)
		const updateData = {
			payment_method: payload.type,
			payment_method_params: {
				checkName: payload.checkName,
				checkAddress: payload.checkAddress,
				achRouting: payload.achRouting,
				achAccount: payload.achAccount,
				paypalEmail: payload.paypalEmail,
				cashTag: payload.cashTag,
			},
		}

		// Update profile with payment method data
		const updated = await updateUserProfile(updateData)
		userStore.setProfile(updated)
		toast.success('Payment method updated successfully!')
		closeEditPaymentMethod()
	} catch (error) {
		console.error('Failed to update payment method:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to update payment method. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isEditPaymentMethodLoading.value = false
	}
}

const governmentIdData = computed(() => {
	// Extract government ID data from user profile
	// Backend should return government_id as string and government_id_params as object
	const profile = userProfile.value
	if (!profile) return null

	// Parse government_id_params if it's a string, otherwise use as-is
	let params = {}
	if (profile.government_id_params) {
		if (typeof profile.government_id_params === 'string') {
			try {
				params = JSON.parse(profile.government_id_params)
			} catch {
				params = {}
			}
		} else {
			params = profile.government_id_params
		}
	}

	return {
		type: profile.government_id || 'state',
		idNumber: params.idNumber || params.id_number || '',
		issuer: params.issuer || '',
	}
})

const openEditDocs = () => {
	isEditDocsOpen.value = true
}

const closeEditDocs = () => {
	isEditDocsOpen.value = false
}

const submitEditDocs = async (payload) => {
	isEditDocsLoading.value = true
	try {
		// Prepare data for backend (no frontend mapping - backend expects exact format)
		const updateData = {
			government_id: payload.type,
			government_id_params: {
				idNumber: payload.idNumber,
				issuer: payload.issuer,
			},
		}

		// Update profile with government ID data
		const updated = await updateUserProfile(updateData)
		userStore.setProfile(updated)
		toast.success('Government ID updated successfully!')
		closeEditDocs()
	} catch (error) {
		console.error('Failed to update government ID:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to update government ID. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isEditDocsLoading.value = false
	}
}

const openEditDateOfBirth = () => {
	isEditDateOfBirthOpen.value = true
}

const closeEditDateOfBirth = () => {
	isEditDateOfBirthOpen.value = false
}

const submitEditDateOfBirth = async (payload) => {
	isEditDateOfBirthLoading.value = true
	try {
		// Prepare data for backend (no frontend mapping - backend expects exact format)
		const updateData = {
			date_of_birth: payload.dateOfBirth || '',
		}

		// Update profile with date of birth
		const updated = await updateUserProfile(updateData)
		userStore.setProfile(updated)
		toast.success('Date of birth updated successfully!')
		closeEditDateOfBirth()
	} catch (error) {
		console.error('Failed to update date of birth:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to update date of birth. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isEditDateOfBirthLoading.value = false
	}
}

const openUploadDocument = () => {
	isUploadDocumentOpen.value = true
}

const closeUploadDocument = () => {
	isUploadDocumentOpen.value = false
}

const submitUploadDocument = async (payload) => {
	isUploadDocumentLoading.value = true
	try {
		if (!payload.file) {
			toast.error('Please select a file to upload.')
			isUploadDocumentLoading.value = false
			return
		}

		// Create FormData for file upload
		const formData = new FormData()
		formData.append('type', payload.type)
		formData.append('file', payload.file)

		// Upload document via API
		const response = await uploadDocument(formData)

		if (response.status && response.document) {
			// Add document to list immediately (optimistic update)
			documents.value = [response.document, ...documents.value]

			toast.success('Document uploaded successfully!')
			closeUploadDocument()
		} else {
			throw new Error(response.error || 'Failed to upload document')
		}
	} catch (error) {
		console.error('Failed to upload document:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to upload document. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isUploadDocumentLoading.value = false
	}
}

const openCreateNewRequest = () => {
	isCreateNewRequestOpen.value = true
}

// Provide function for AccountNavbar to open confirmation modal
// Must be after function definition - update the ref from MainLayout
const openAccountKitRequest = inject('openAccountKitRequest', null)
if (openAccountKitRequest) {
	openAccountKitRequest.value = openCreateNewRequest
}

const closeCreateNewRequest = () => {
	isCreateNewRequestOpen.value = false
}

const submitOfferResponse = async (orderId, action) => {
	offerResponding.value = action
	try {
		await respondToOffer(orderId, action)
		toast.success(action === 'accept' ? 'Offer accepted. We will process your payment soon.' : 'Offer declined. We will return your items.')
		await loadUserData()
	} catch (error) {
		console.error('Offer response failed:', error)
		const msg = error?.data?.message || error?.message || 'Failed to submit response. Please try again.'
		toast.error(msg)
	} finally {
		offerResponding.value = null
	}
}

const submitCreateNewRequest = async () => {
	isCreateNewRequestLoading.value = true
	try {
		const response = await createKitRequest()

		if (response.status && response.order_id) {
			// Get user name from profile
			const userName = userProfile.value?.first_name?.toUpperCase() || userProfile.value?.name?.toUpperCase() || 'USER'

			// Redirect to success page with order ID and user name
			const queryParams = new URLSearchParams({
				name: userName,
				order_id: response.order_id.toString(),
			})

			router.push(`/user/kit-request-success?${queryParams.toString()}`)
		} else {
			throw new Error(response.error || 'Failed to create kit request')
		}
	} catch (error) {
		console.error('Failed to create kit request:', error)
		if (error?.status === 401 || error.response?.status === 401) {
			toast.error('Session expired. Please sign in again.')
			router.push('/sign-in')
		} else {
			const errorMessage = error?.data?.message || error?.message || 'Failed to create kit request. Please try again.'
			toast.error(errorMessage)
		}
	} finally {
		isCreateNewRequestLoading.value = false
	}
}

onMounted(() => {
	// Hide any legacy Bootstrap modals that might conflict with Vue modals
	if (typeof document !== 'undefined') {
		const legacyModal = document.getElementById('createNewRequest')
		if (legacyModal && legacyModal.classList.contains('modal')) {
			// Check if it's a legacy form-based modal (has form inside)
			const form = legacyModal.querySelector('form[action*="orders/create"]')
			if (form) {
				// Remove legacy modal to prevent conflicts
				legacyModal.remove()
			}
		}
	}

	loadUserData()
})
</script>

<style scoped>
.offer-panel.alert-success {
	background-color: #6eb55c;
	border-color: #5a9e4a;
	color: #fff;
}

.offer-panel .offer-amount {
	font-size: 1.75rem;
	font-weight: 700;
	white-space: nowrap;
}

.offer-panel .offer-order-id {
	font-size: 0.875rem;
	opacity: 0.9;
}

.offer-panel .offer-title {
	font-weight: 700;
	font-size: 1.1rem;
}

.offer-panel .offer-desc {
	font-weight: 600;
}

.offer-panel .offer-desc-small {
	font-size: 0.9rem;
	opacity: 0.95;
}

.offer-panel a.text-dark:hover {
	text-decoration: underline;
}
</style>
