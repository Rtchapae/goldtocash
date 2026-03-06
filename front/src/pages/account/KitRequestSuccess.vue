<template>
	<div class="kit-success-page">
		<div
			v-if="showModal && userEmail && userPassword"
			class="kit-success-modal-backdrop"
		>
			<div class="modal-dialog" role="document">
				<div class="modal-content text-center">
					<div class="m-5">
						<h3>Create <span style="color: var(--primary);">an Account</span></h3>
						<p>
							You will need an&nbsp;account to&nbsp;check the status of&nbsp;your appraisal process, track your package,
							respond to&nbsp;our offer, view and edit your personal and payment information.
						</p>
						<h4>Username: <span>{{ userEmail }}</span></h4>
						<p>Your username is&nbsp;the email address you provided.</p>
						<h4 id="AccountModalPass">Password: <span>{{ userPassword }}</span></h4>
						<p>Here is your secure password.</p>
						<p>For your record we have sent you an email containing your password and username.</p>
						<button
							id="submitGetPass"
							class="btn btn-primary"
							@click="handleCreateAccount"
						>
							Create My&nbsp;Account
						</button>
					</div>
				</div>
			</div>
		</div>

		<section class="content" :class="{ 'blurred': showModal }">
			<div class="sidebar-purple text-center">
			<div class="d-md-flex justify-content-center align-items-center">
				<img src="/images/phone-call-gold-icon.svg" alt="Icon">
				&nbsp;
				&nbsp;
				<span class="gold">Call or text us at <a href="tel:5642377332">564-237-7332</a></span>
				&nbsp;
				<span>to connect with a live representative. We value your interest and look forward to helping you!</span>
			</div>
		</div>
		<div class="container pt-5">
			<div class="mb-5">
				<p class="pb-3" style="text-align:center;color:var(--primary);letter-spacing: 1px;">
					{{ userName }}, THANKS FOR REQUESTING A KIT FROM US
				</p>
				<div id="dashed-border"></div>
			</div>

			<div>
				<div class="row">
					<div class="col-lg-7 p-2 text-center">
						<h1>You'll receive your<br>appraisal kit within 3-5<br> business days.</h1>
						<p class="mt-3">
							The kit contains everything you need to ship your valuables to Gold to Cash.
							<br>
							(We've also emailed it to you!)
						</p>

						<div style="border-top:solid 2px black;margin-top:50px;">
							<p style="background:#F2F2F2;padding:3px 20px;margin:-15px auto 0;max-width:fit-content;letter-spacing:1px;">
								WHAT'S NEXT:
							</p>
						</div>

						<div id="whats-next-items" class="d-flex justify-content-between">
							<div class="whats-next-item">
								<img src="/images/mailbox.png" class="w-100">
								<p>Receive your pre-paid FedEx shipping label</p>
							</div>
							<div class="whats-next-item">
								<img src="/images/bag-of-gold.png" class="w-100">
								<p>Pack your valuables</p>
							</div>
							<div class="whats-next-item">
								<img src="/images/fedex-store-front.png" class="w-100">
								<p>Take it to a FedEx store &amp; ship it out</p>
							</div>
						</div>

						<div id="assurances" class="mt-5">
							<p>
								<img src="/images/check-icon.svg">
								100% SECURE
							</p>
							<p>
								<img src="/images/check-icon.svg">
								100% FREE
							</p>
							<p>
								<img src="/images/check-icon.svg">
								100% SATISFACTION
							</p>
						</div>
					</div>

					<div class="col-lg-5">
						<div id="print-block">
							<div class="text-center">
								<h4>Want to Get Paid Faster?</h4>
								<p style="font-size:.9em;">We've emailed your free shipping label!</p>
							</div>

							<div class="print-step">
								<span class="print-step-num">
									<span class="print-step-num-text">1</span>
								</span>
								PRINT YOUR FREE SHIPPING LABEL
							</div>

							<div id="no-printer-block">
								<img src="https://goldtocash.us/images/no-printer-no-bg.png">
								<p class="mb-0">
									<b>No printer? No problem!</b>
									Take our email to a FedEx store and they will scan &amp; print your shipping label from the email.
								</p>
							</div>

							<div class="print-step">
								<span class="print-step-num">
									<span class="print-step-num-text">2</span>
								</span>
								PACK YOUR VALUABLES
							</div>
							<div class="print-step">
								<span class="print-step-num">
									<span class="print-step-num-text">3</span>
								</span>
								TAKE IT TO FEDEX STORE &amp; SHIP IT OUT
							</div>

							<p style="color:#989898;font-size:.7em;text-align:center;margin-top:12px;">
								We will appraise your valuables and have an offer for you the same day we receive your package.
							</p>

							<button 
								v-if="orderId" 
								id="print-btn" 
								class="btn btn-kit btn-purple w-100"
								:disabled="isLoadingLabel"
								@click="handlePrintClick"
							>
								<span v-if="isLoadingLabel" class="spinner-border spinner-border-sm me-2" role="status"></span>
								{{ isLoadingLabel ? 'Loading...' : 'Print My Label' }}
							</button>
						</div>

						<div style="padding:0 3em;margin: 1.5em 0 3em;">
							<a href="/user/account" class="btn btn-dark w-100">
								Go to My Account
							</a>
						</div>
					</div>

					<div class="col mb-5">
						<p style="border:dashed 1px black;border-radius:.6em;padding:1em;text-align:center;">
							We will have an offer for you the same day we receive your package! Check the status of your shipment and the appraisal process in your account.
							Your package is fully insured up to $50,000*
						</p>
					</div>
				</div>
			</div>
		</div>
		</section>
	</div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useToast } from '@/composables/useToast'
import { useUserStore } from '@/stores/user'
import { getPrintLabel, confirmAccountCreation } from '@/api/kitRegistration'

const route = useRoute()
const toast = useToast()
const userStore = useUserStore()
const isLoadingLabel = ref(false)
const showModal = ref(false)

const userName = computed(() => {
	return route.query.name || route.params.name || 'USER'
})

const orderId = computed(() => {
	return route.query.order_id || route.params.order_id || null
})

const userEmail = computed(() => {
	return route.query.email || null
})

const userPassword = computed(() => {
	return route.query.password || null
})

const handleCreateAccount = async () => {
	try {
		if (orderId.value) {
			await confirmAccountCreation(orderId.value)
			localStorage.setItem(`account_confirmed_${orderId.value}`, 'true')
			toast.success('Account created successfully!')
		}
	} catch (error) {
		console.error('Failed to confirm account creation:', error)
		toast.error('Failed to create account. Please try again.')
	} finally {
		showModal.value = false
		if (typeof document !== 'undefined') {
			document.body.style.overflow = ''
		}
	}
}

watch(showModal, (isOpen) => {
	if (typeof document === 'undefined') return
	
	if (isOpen) {
		document.body.style.overflow = 'hidden'
	} else {
		document.body.style.overflow = ''
	}
})

onUnmounted(() => {
	if (typeof document !== 'undefined') {
		document.body.style.overflow = ''
	}
})

onMounted(async () => {
	const token = localStorage.getItem('jwt_token')
	if (token && !userStore.profile) {
		try {
			await userStore.loadProfile()
		} catch (error) {
			console.error('Failed to load user profile:', error)
		}
	}

	const accountConfirmed = localStorage.getItem(`account_confirmed_${orderId.value}`) === 'true'

	if (userEmail.value && userPassword.value && !accountConfirmed) {
		showModal.value = true
		document.body.style.overflow = 'hidden'
	}

	if (typeof gtag === 'function') {
		gtag('event', 'conversion', {
			'send_to': 'AW-596591835/z-ZACKT5teECENuJvZwC'
		})
	}

	if (typeof fbq === 'function') {
		fbq('track', 'SubmitApplication')
		fbq('track', 'Lead', {
			value: 2,
			currency: 'usd',
		})
	}

	(function(w, d, t, r, u) {
		var f, n, i
		w[u] = w[u] || [], f = function() {
			var o = { ti: "27016418" }
			o.q = w[u], w[u] = new UET(o), w[u].push("pageLoad")
		},
			n = d.createElement(t), n.src = r, n.async = 1, n.onload = n.onreadystatechange = function() {
				var s = this.readyState
				s && s !== "loaded" && s !== "complete" || (f(), n.onload = n.onreadystatechange = null)
			},
			i = d.getElementsByTagName(t)[0], i.parentNode.insertBefore(n, i)
	})(window, document, "script", "//bat.bing.com/bat.js", "uetq")
})

const handlePrintClick = async (event) => {
	event.preventDefault()
	
	if (!orderId.value) {
		console.error('Order ID is missing')
		return
	}

	isLoadingLabel.value = true

	try {
		console.log('Requesting print label for order:', orderId.value)

		const response = await getPrintLabel(orderId.value)
		console.log('Response received:', {
			ok: response.ok,
			status: response.status,
			statusText: response.statusText,
			headers: Object.fromEntries(response.headers.entries())
		})

		if (response.ok) {
			const contentType = response.headers.get('content-type')
			console.log('Content-Type:', contentType)

			const blob = await response.blob()
			console.log('Blob created:', { size: blob.size, type: blob.type })

			const fileUrl = URL.createObjectURL(blob)
			console.log('Object URL created:', fileUrl)

			const newWindow = window.open(fileUrl, '_blank')

			if (newWindow) {
				toast.success('Shipping label opened in new window')
			} else {
				const link = document.createElement('a')
				link.href = fileUrl
				link.download = `shipping-label-${orderId.value}.png`
				document.body.appendChild(link)
				link.click()
				document.body.removeChild(link)
				toast.success('Shipping label downloaded')
			}

			setTimeout(() => {
				URL.revokeObjectURL(fileUrl)
			}, 10000)
		} else {
			let errorMsg = 'Failed to retrieve shipping label. Please try again later.'
			try {
				const errorData = await response.json()
				errorMsg = errorData.error || errorMsg
			} catch (e) {
				errorMsg = response.statusText || errorMsg
			}
			toast.error(errorMsg)
		}
	} catch (error) {
		console.error('Error getting print label:', error)
		const errorMsg = error?.message || error?.data?.message || 'An error occurred while retrieving the shipping label. Please try again later.'
		toast.error(errorMsg)
	} finally {
		isLoadingLabel.value = false
	}
}

</script>

<style scoped>
body {
	background: #F2F2F2;
}

.content.blurred {
	filter: blur(5px);
	pointer-events: none;
	user-select: none;
}

.kit-success-page {
	position: relative;
	margin-top: 5rem;
}

.kit-success-modal-backdrop {
	position: fixed;
	inset: 0;
	z-index: 1050;
	display: flex;
	align-items: center;
	justify-content: center;
	background-color: rgba(0, 0, 0, 0.5);
}

.kit-success-modal-backdrop .modal-dialog {
	margin: 1.75rem auto;
	max-width: 500px;
	background-color: #fff;
}

.kit-success-modal-backdrop .modal-content {
	border-radius: 0.5rem;
	border: none;
	box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.kit-success-modal-backdrop .modal-content h3 {
	margin-bottom: 1rem;
	font-weight: 600;
}

.kit-success-modal-backdrop .modal-content h4 {
	margin-top: 1.5rem;
	margin-bottom: 0.5rem;
	font-size: 1.1rem;
	font-weight: 600;
}

.kit-success-modal-backdrop .modal-content h4 span {
	color: var(--primary);
	font-weight: 700;
}

.kit-success-modal-backdrop .modal-content p {
	margin-bottom: 1rem;
	font-size: 0.95rem;
	line-height: 1.5;
}

.kit-success-modal-backdrop .modal-content #AccountModalPass {
	margin-top: 1rem;
}

.kit-success-modal-backdrop .modal-content .btn-primary {
	background-color: var(--primary);
	border-color: var(--primary);
	padding: 0.75rem 2rem;
	font-weight: 600;
	margin-top: 1rem;
}
</style>

<style scoped>
#dashed-border {
	height: 2px;
	background-image: linear-gradient(to right, var(--primary) 65%, rgba(255, 255, 255, 0) 0%);
	background-position: bottom;
	background-size: 15px 1px;
	background-repeat: repeat-x;
}

.sidebar-purple {
	background: #8D27DC !important;
	box-shadow: 0 8px 8px rgba(140, 39, 220, 0.25);
	padding: 15px;
	margin-top: -50px;
}

.sidebar-purple span {
	color: #fff;
}

.sidebar-purple .gold,
.sidebar-purple .gold a {
	color: var(--primary);
	letter-spacing: 1px;
}

.sidebar-purple .gold a:hover {
	color: #fff;
	text-decoration: none;
}

@media (max-width: 767px) {
	#whats-next-items {
		flex-wrap: wrap;
		justify-content: center !important;
	}

	.whats-next-item {
		width: 100%;
	}

	.whats-next-item img {
		min-width: 50%;
		max-width: 30vw;
	}

	.sidebar-purple {
		margin-top: 0px;
	}
}

.whats-next-item {
	padding: 1em;
	min-width: 8em;
}

.whats-next-item p {
	margin-top: 1em;
	font-size: .9em;
}

#assurances {
	display: flex;
	justify-content: space-around;
}

#assurances p {
	position: relative;
	padding: 0 1em;
}

@media (max-width: 767px) {
	#assurances {
		display: block;
	}
}

#print-block {
	position: relative;
	background: white;
	padding: 3em;
	box-shadow: rgba(0, 0, 0, 0.1) 0 0 10px;
	margin-bottom: 5em;
}

#print-block::before {
	content: '';
	width: 100%;
	height: 8px;
	position: absolute;
	top: -8px;
	left: 0;
	background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><polygon points="0,100 50,0 100,100" style="fill:white;"/></svg>');
	background-repeat: repeat-x;
	background-size: 18px 9px;
}

#print-block::after {
	content: '';
	width: 100%;
	height: 8px;
	position: absolute;
	bottom: -8px;
	left: 0;
	background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><polygon points="0,0 100,0 50,100" style="fill:white;"/></svg>');
	background-repeat: repeat-x;
	background-size: 18px 9px;
}

.print-step {
	display: flex;
	justify-content: start;
	padding: 1em 0;
	letter-spacing: 1px;
}

.print-step-num {
	border-radius: 100%;
	border: solid 2px var(--primary);
	text-align: center;
	height: 27px;
	width: 27px;
	min-width: 27px;
	display: inline-block;
	vertical-align: middle;
	margin-right: 10px;
}

.print-step-num-text {
	display: inline-block;
	vertical-align: middle;
	color: var(--primary);
	text-align: center;
	margin-top: -1px;
	margin-right: -2px;
}

#no-printer-block {
	display: flex;
	background: #FBF5E8;
	padding: 1em;
	border-radius: .75em;
	align-items: center;
	margin-bottom: 20px;
}

#no-printer-block p {
	font-size: .82em;
}

#no-printer-block img {
	max-width: 3em;
	padding: 0 1em 0 0;
}
</style>

