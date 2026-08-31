<template>
	<div class="contact-page">
		<section class="contact-hero" aria-labelledby="contact-hero-title">
			<div class="contact-hero__inner">
				<div class="contact-hero__info">
					<h1 id="contact-hero-title" class="contact-hero__title">
						Questions about our process? Wondering what you should or should not send in? Safety or security
						concerns?
					</h1>
					<p class="contact-hero__lead">
						Our top priority is you, the customer. Feel free to contact us and we'll get back to you as soon as
						possible!
					</p>

					<ul class="contact-hero__details" role="list">
						<li class="contact-hero__detail contact-hero__detail--office">
							<img
								class="contact-hero__icon"
								src="/images/contact-pin-icon.svg"
								alt=""
								width="24"
								height="24"
								decoding="async"
							/>
							<div class="contact-hero__detail-body">
								<p class="contact-hero__detail-label">Office</p>
								<p class="contact-hero__detail-text">
									1101 Broadway St Suite 230A Vancouver WA 98682
								</p>
								<p class="contact-hero__detail-text">Office Hours: Mon-Fri 9am - 5pm</p>
							</div>
						</li>
						<li class="contact-hero__detail-row">
							<div class="contact-hero__detail">
								<img
									class="contact-hero__icon"
									src="/images/phone-call-gold-icon.svg"
									alt=""
									width="24"
									height="24"
									decoding="async"
								/>
								<div class="contact-hero__detail-body">
									<p class="contact-hero__detail-label">Call or Text</p>
									<p class="contact-hero__detail-text">
										<a href="tel:5642377332">564.237.7332</a>
									</p>
								</div>
							</div>
							<div class="contact-hero__detail">
								<img
									class="contact-hero__icon"
									src="/images/mail-icon.svg"
									alt=""
									width="24"
									height="24"
									decoding="async"
								/>
								<div class="contact-hero__detail-body">
									<p class="contact-hero__detail-label">Email Us</p>
									<p class="contact-hero__detail-text">
										<a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a>
									</p>
								</div>
							</div>
						</li>
					</ul>
				</div>

				<form class="contact-hero__form" action="/sendmail" method="post" @submit.prevent="onSubmit">
					<h2 class="contact-hero__form-title">Get in Touch With the Gold to Cash Team.</h2>

					<label class="contact-hero__field">
						<span class="contact-hero__field-label">Full Name</span>
						<input
							v-model="form.name"
							class="contact-hero__input"
							type="text"
							name="name"
							required
							autocomplete="name"
							:disabled="submitting"
						/>
					</label>
					<label class="contact-hero__field">
						<span class="contact-hero__field-label">Email Address</span>
						<input
							v-model="form.email"
							class="contact-hero__input"
							type="email"
							name="email"
							required
							autocomplete="email"
							:disabled="submitting"
						/>
					</label>
					<label class="contact-hero__field">
						<span class="contact-hero__field-label">Phone Number</span>
						<input
							v-model="form.phone"
							class="contact-hero__input"
							type="tel"
							name="phone"
							required
							autocomplete="tel"
							:disabled="submitting"
						/>
					</label>
					<label class="contact-hero__field contact-hero__field--message">
						<span class="contact-hero__field-label">Message</span>
						<textarea
							v-model="form.message"
							class="contact-hero__input contact-hero__textarea"
							name="message"
							rows="4"
							required
							maxlength="999"
							:disabled="submitting"
						/>
					</label>

					<label class="contact-hero__field contact-hero__field--captcha">
						<span class="contact-hero__field-label">
							Captcha: what is {{ captcha.question || '…' }}?
						</span>
						<input
							v-model="form.captcha_answer"
							class="contact-hero__input"
							type="text"
							inputmode="numeric"
							name="captcha_answer"
							required
							autocomplete="off"
							:disabled="submitting || !captcha.token"
							placeholder="Your answer"
						/>
					</label>

					<div v-if="statusMessage" id="success" class="contact-hero__status" :class="statusClass" role="status">
						{{ statusMessage }}
					</div>

					<button type="submit" class="contact-hero__submit" :disabled="submitting || !captcha.token">
						{{ submitting ? 'Sending…' : 'Send Message' }}
					</button>
				</form>
			</div>
		</section>

		<section class="contact-faq" aria-labelledby="contact-faq-title">
			<div class="contact-faq__inner">
				<h2 id="contact-faq-title" class="contact-faq__title">FAQs</h2>
				<div class="contact-faq__list">
					<details v-for="(item, idx) in faqItems" :key="idx" class="contact-faq__item">
						<summary class="contact-faq__question">
							<span class="contact-faq__question-text">{{ item.question }}</span>
							<span class="contact-faq__toggle" aria-hidden="true" />
						</summary>
						<div class="contact-faq__answer">
							<p>{{ item.answer }}</p>
						</div>
					</details>
				</div>
			</div>
		</section>
	</div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { getContactCaptcha, sendContactMessage } from '@/api/contact.js'
import seoService from '@/services/seoService.js'

/** Figma 1018:2644 contact + 1017:1599 FAQ */
const faqItems = [
	{
		question: 'Do you accept diamonds, stones or gems?',
		answer:
			'At Gold to Cash, we specialize in giving you the best price for your Gold & Silver. While our pricing is focus on precious metal components, we will factor in small stones if they happen to be in your jewelry. However, we do not accept individual or lose stones.',
	},
	{
		question: 'Is my shipment insured?',
		answer:
			'Absolutely. Our shipping process includes tracking updates and insurance to help protect your valuables as they make their way to us.',
	},
	{
		question: 'What if I decline the offer from Gold to Cash?',
		answer:
			'If you choose to not accept our offer, your item can be returned to you according to our return policy.',
	},
	{
		question: 'Am I obligated to sell?',
		answer:
			'No. Our shipping kit, and evaluation process is free for our customers, and you are not obligated to sell.',
	},
	{
		question: 'How do you determine the price of my gold?',
		answer:
			'We weigh and test each item for purity, then base your offer on current market prices so you know exactly what you are being paid for.',
	},
	{
		question: 'How do I get in touch with the Gold to Cash team?',
		answer:
			'We are happy to help! You can reach us by email: hello@goldtocash.us, phone: 564.237.7332, or during office hours at 1101 Broadway St Suite 230A Vancouver, WA 98682.',
	},
]

const form = reactive({
	name: '',
	email: '',
	phone: '',
	message: '',
	captcha_answer: '',
})

const captcha = reactive({
	a: null,
	b: null,
	expires: null,
	token: '',
	question: '',
})

const submitting = ref(false)
const statusMessage = ref('')
const statusOk = ref(false)
const statusClass = computed(() =>
	statusOk.value ? 'contact-hero__status--success' : 'contact-hero__status--error',
)

const loadCaptcha = async () => {
	try {
		const data = await getContactCaptcha()
		captcha.a = data.a
		captcha.b = data.b
		captcha.expires = data.expires
		captcha.token = data.token
		captcha.question = data.question
		form.captcha_answer = ''
	} catch {
		captcha.token = ''
		captcha.question = ''
		statusOk.value = false
		statusMessage.value = 'Could not load captcha. Please refresh the page.'
	}
}

const onSubmit = async () => {
	statusMessage.value = ''
	if (!captcha.token) {
		statusOk.value = false
		statusMessage.value = 'Captcha is not ready. Please refresh and try again.'
		return
	}
	submitting.value = true
	try {
		const data = await sendContactMessage({
			...form,
			captcha_a: captcha.a,
			captcha_b: captcha.b,
			captcha_expires: captcha.expires,
			captcha_token: captcha.token,
			captcha_answer: Number.parseInt(String(form.captcha_answer).trim(), 10),
		})
		statusOk.value = true
		statusMessage.value =
			data?.message || 'Your message has been sent. We will get back to you soon!'
		form.name = ''
		form.email = ''
		form.phone = ''
		form.message = ''
		form.captcha_answer = ''
		await loadCaptcha()
	} catch (err) {
		statusOk.value = false
		statusMessage.value =
			err?.data?.message ||
			err?.message ||
			'Sorry, something went wrong. Please try again or email hello@goldtocash.us.'
		await loadCaptcha()
	} finally {
		submitting.value = false
	}
}

onMounted(() => {
	seoService.setMeta({
		title: 'Contact Us | Gold To Cash',
		description:
			'Questions about selling gold? Contact Gold to Cash by phone, email, or form. Office in Vancouver, WA. Fast responses Mon–Fri 9am–5pm.',
	})
	loadCaptcha()
})
</script>

<style scoped>
.contact-page {
	width: 100%;
}

/* —— Contact hero (Figma 1018:2644) —— */
.contact-hero {
	width: 100%;
	background: #fff;
	padding: 160px 0 100px;
	box-sizing: border-box;
}

.contact-hero__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: grid;
	grid-template-columns: minmax(0, 1.05fr) minmax(0, 0.95fr);
	gap: 64px;
	align-items: start;
}

.contact-hero__info {
	display: flex;
	flex-direction: column;
	gap: 28px;
	min-width: 0;
}

.contact-hero__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 32px;
	line-height: 32px;
	letter-spacing: 0;
	vertical-align: middle;
	color: #000;
}

.contact-hero__lead {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.6vw, 18px);
	line-height: 1.45;
	color: #000;
}

.contact-hero__details {
	list-style: none;
	margin: 12px 0 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 28px;
	width: 100%;
}

.contact-hero__detail {
	display: flex;
	align-items: flex-start;
	gap: 14px;
	min-width: 0;
}

.contact-hero__detail--office {
	width: 100%;
}

.contact-hero__detail-body {
	flex: 1;
	min-width: 0;
	width: 100%;
}

.contact-hero__detail-row {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 24px;
	width: 100%;
	align-items: start;
}

.contact-hero__icon {
	flex-shrink: 0;
	width: 24px;
	height: 24px;
	margin-top: 2px;
}

.contact-hero__detail-label {
	margin: 0 0 6px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 18px;
	line-height: 1.2;
	color: #000;
}

.contact-hero__detail-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 16px;
	line-height: 1.4;
	color: #000;
	max-width: none;
	width: 100%;
}

.contact-hero__detail-text a {
	color: #000;
	text-decoration: underline;
	text-underline-offset: 2px;
}

.contact-hero__form {
	display: flex;
	flex-direction: column;
	gap: 22px;
	padding: 36px 32px 32px;
	background: #fff;
	border-radius: 16px;
	box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
	box-sizing: border-box;
	min-width: 0;
}

.contact-hero__form-title {
	margin: 0 0 8px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.25rem, 2vw, 24px);
	line-height: 1.25;
	color: #000;
}

.contact-hero__field {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.contact-hero__field-label {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: 14px;
	line-height: 1.2;
	color: #666;
}

.contact-hero__input {
	width: 100%;
	border: 0;
	border-bottom: 1px solid #cfcfcf;
	border-radius: 0;
	background: transparent;
	padding: 8px 0 12px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 16px;
	line-height: 1.3;
	color: #000;
	box-sizing: border-box;
	outline: none;
}

.contact-hero__input:focus {
	border-bottom-color: #c39e3d;
}

.contact-hero__textarea {
	resize: vertical;
	min-height: 96px;
}

.contact-hero__submit {
	margin-top: 8px;
	width: 100%;
	border: 0;
	border-radius: 8px;
	background: #000;
	color: #fff;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 16px;
	line-height: 1;
	padding: 18px 24px;
	cursor: pointer;
}

.contact-hero__submit:hover {
	background: #222;
}

.contact-hero__submit:disabled {
	opacity: 0.55;
	cursor: not-allowed;
}

.contact-hero__status {
	margin: 4px 0 0;
	border-radius: 2px;
	color: #fff;
	padding: 1em 1.25em;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 14px;
	line-height: 1.4;
}

.contact-hero__status--success {
	background-color: green;
	border-left: 0.618em solid rgba(0, 0, 0, 0.15);
}

.contact-hero__status--error {
	background-color: red;
	border-left: 0.618em solid rgba(0, 0, 0, 0.15);
}

/* —— FAQ (Figma 1017:1599) —— */
.contact-faq {
	width: 100%;
	background: #c39e3d;
	padding: 100px 0;
	box-sizing: border-box;
}

.contact-faq__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.contact-faq__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 1.15;
	text-align: center;
	color: #fff9ee;
}

.contact-faq__list {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.contact-faq__item {
	border: 3px solid #000;
	background: #fff;
	box-sizing: border-box;
	width: 100%;
}

.contact-faq__question {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	min-height: 80px;
	padding: 14px 20px;
	cursor: pointer;
	list-style: none;
	box-sizing: border-box;
	background: #fff;
	color: #000;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.125rem, 2vw, 24px);
	line-height: 1.2;
}

.contact-faq__question::-webkit-details-marker {
	display: none;
}

.contact-faq__item[open] .contact-faq__question {
	background: #000;
	color: #fff;
}

.contact-faq__question-text {
	flex: 1;
}

.contact-faq__toggle {
	position: relative;
	flex-shrink: 0;
	width: 18px;
	height: 18px;
}

.contact-faq__toggle::before,
.contact-faq__toggle::after {
	content: '';
	position: absolute;
	background: #000;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
}

.contact-faq__toggle::before {
	width: 18px;
	height: 3px;
}

.contact-faq__toggle::after {
	width: 3px;
	height: 18px;
}

.contact-faq__item[open] .contact-faq__toggle::before,
.contact-faq__item[open] .contact-faq__toggle::after {
	background: #fff;
}

.contact-faq__item[open] .contact-faq__toggle::after {
	display: none;
}

.contact-faq__answer {
	padding: 16px 20px 20px;
	background: #fff;
	box-sizing: border-box;
}

.contact-faq__answer p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1.4;
	color: #000;
}

@media (max-width: 991.98px) {
	.contact-hero {
		padding: 120px 0 64px;
	}

	.contact-hero__inner {
		grid-template-columns: 1fr;
		gap: 40px;
	}

	.contact-hero__info {
		align-items: center;
		text-align: center;
	}

	.contact-hero__title {
		font-size: 28px;
		line-height: 28px;
		text-align: center;
	}

	.contact-hero__lead {
		text-align: center;
	}

	.contact-hero__details {
		align-items: stretch;
		width: 100%;
	}

	.contact-hero__detail--office {
		text-align: left;
	}

	.contact-hero__detail-row {
		display: flex;
		flex-direction: column;
		gap: 28px;
		width: 100%;
	}

	.contact-hero__detail-row .contact-hero__detail {
		text-align: left;
	}

	.contact-hero__form {
		padding: 28px 20px 24px;
		box-shadow: 0 8px 28px rgba(0, 0, 0, 0.08);
	}

	.contact-faq {
		padding: 64px 0 72px;
	}

	.contact-faq__inner {
		gap: 28px;
	}

	.contact-faq__question {
		min-height: 64px;
		padding: 14px 16px;
	}
}
</style>
