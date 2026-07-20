<template>
	<section
		id="gold-calculator-faq"
		class="gc-faq"
		aria-labelledby="gc-faq-heading"
	>
		<div class="gc-faq__inner">
			<header class="gc-faq__header">
				<h2 id="gc-faq-heading" class="gc-faq__page-title">
					<span class="gc-faq__page-title-part gc-faq__page-title-part--cream">FAQs</span>
					<span
						v-if="variant !== 'how-sell' && variant !== 'what-we-buy' && variant !== 'what-sets-apart'"
						class="gc-faq__page-title-part gc-faq__page-title-part--ink"
					> About Gold Value</span>
				</h2>
			</header>

			<div class="gc-faq__groups">
				<div v-for="block in visibleSections" :key="block.id" class="gc-faq__block">
					<h3 v-if="block.title" :id="`gc-faq-${block.id}`" class="gc-faq__block-title">{{ block.title }}</h3>
					<div class="gc-faq__list">
						<details v-for="(item, idx) in block.items" :key="idx" class="gc-faq__item">
							<summary class="gc-faq__question">
								<span class="gc-faq__question-text">{{ item.question }}</span>
								<span class="gc-faq__chev" aria-hidden="true" />
							</summary>
							<div class="gc-faq__answer">
								<p>{{ item.answer }}</p>
							</div>
						</details>
					</div>
				</div>
			</div>
		</div>

		<div class="seo-faq-schema" aria-hidden="true">
			<FaqJsonLd :items="flatFaqItems" />
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import FaqJsonLd from '@/components/seo/FaqJsonLd.vue'

const props = defineProps({
	/** Which FAQ set to show (calculator / scrap / dental routes). */
	variant: {
		type: String,
		default: 'calculator',
		validator: (v) => ['calculator', 'scrap', 'dental', 'how-sell', 'what-we-buy', 'what-sets-apart'].includes(v),
	},
})

const sectionBlocks = [
	{
		id: 'calculator',
		title: '',
		items: [
			{
				question: 'How accurate is the calculator?',
				answer:
					'Our calculator provides an up-to-date estimate based on the gold spot price, but your final offer depends on purity and actual weight verified by our team.',
			},
			{
				question: 'Can I sell mixed gold items?',
				answer:
					'Yes. Even if you have 10K and 14K pieces together, our team will test and weigh each separately for maximum payout accuracy.',
			},
			{
				question: 'What if my gold has stones or enamel?',
				answer:
					'No problem. We’ll remove non-gold materials during the appraisal and base your offer solely on the gold content.',
			},
			{
				question: 'How do I get paid?',
				answer:
					'Once we receive and evaluate your gold, you’ll receive an offer within 24 hours with payment options.',
			},
		],
	},
	{
		id: 'dental',
		title: '',
		items: [
			{
				question: 'How much is a gold tooth worth?',
				answer:
					'It depends on the weight, purity, and gold value at the time of calculation. Most single crowns are worth between $40 and $100 based on average gold content.',
			},
			{
				question: 'Can I sell dental gold with porcelain still attached?',
				answer:
					'Yes. Our refining partners safely remove and separate non-metal materials, so you don’t need to clean or break your items.',
			},
			{
				question: 'Do I have to be a dentist to sell dental gold?',
				answer: 'No. We work with individuals, dental offices, and estates — anyone with legitimate dental gold.',
			},
			{
				question: 'Is it legal to sell dental gold?',
				answer: 'Absolutely. Once dental work is removed and belongs to you, you’re free to sell it.',
			},
			{
				question: 'Do you buy platinum or palladium dental alloys?',
				answer:
					'Yes. Many dental pieces contain precious metals beyond gold. We test and pay for all identifiable metal content.',
			},
		],
	},
	{
		id: 'scrap',
		title: '',
		items: [
			{
				question: 'What counts as “scrap gold”?',
				answer:
					'Any gold that’s broken, worn out, or no longer used — like old jewelry, watch cases, coins, or leftover gold from repairs — qualifies as scrap gold.',
			},
			{
				question: 'Can I sell gold-plated items?',
				answer:
					'We only purchase solid gold, not plated or filled items. If you’re unsure, we’ll test and let you know before finalizing your offer.',
			},
			{
				question: 'Do I need to clean or sort my gold before sending it?',
				answer: 'No. Just include everything in your mail-in kit. Our experts handle cleaning, sorting, and testing.',
			},
			{
				question: 'Is it safe to mail gold?',
				answer:
					'Yes. Our prepaid shipping is fully insured and trackable through USPS for up to $5,000.',
			},
			{
				question: 'Do you buy other precious metals?',
				answer: 'Yes. We also buy silver, platinum, and palladium scrap.',
			},
		],
	},
	{
		id: 'how-sell',
		title: '',
		items: [
			{
				question: 'Do I need to clean or sort my gold before sending it in?',
				answer:
					'No need! Our experts handle any testing, sorting as well as cleaning. All you need it to include everything you’d like to sell in the mail-in kit',
			},
			{
				question: 'How safe is it to mail gold?',
				answer:
					'Very Safe! Our prepaid shipping kit is trackable and fully insured through USPS for up to $5,000. If you have any questions, you can always give us a call at 564.237.7332 We are open Monday through Friday 9am - 5pm PST',
			},
			{
				question: 'How long does the process take?',
				answer:
					'It generally comes down to the shipping time. However, once the item is in our possession, you will receive a payout offer from us within 24 hours. That said, the process can take up to 5 business days.',
			},
			{
				question: 'How do I get paid?',
				answer:
					'We want to make every step of the process convenient and fast. That is why we offer four different payment options. Wire Transfer / ACH: Fast and Free! Direct deposit into your bank account within 1-3 business days. Company Check: Mailed to the address we have on file within 24 hours. Default payment method if you leave a payment option field blank in the Information Card or if there is an issue with using another payment option. Cash App: Fast and Free! A very popular and convenient option if you want to get paid faster. PayPal: Similar to Cash App - a free & convenient option if you want to get paid faster.',
			},
		],
	},
	{
		id: 'what-we-buy',
		title: '',
		items: [
			{
				question: 'What is Scrap Gold?',
				answer:
					'Scrap gold is just a general term for any broken, worn out, or no longer used gold. Any old jewelry, watch cases, coins, or unrepaired gold qualifies as scrap gold. If you are unsure, feel free to send in your items for zero charge and our team will help identify what you may have',
			},
			{
				question: 'Do you accept used or damaged items?',
				answer:
					'Your gold necklace is broken? Ring is dented? Selling a gold crown? No worries! We accept items in any condition! Our concern is only the weight and purity of the gold item, any other factors do not affect the price of our payout.',
			},
			{
				question: 'How Do I Know If My Item Is Fully Gold or Gold Plated?',
				answer:
					'Without the proper tools and equipment, knowing the difference between what is pure gold or only gold-plated can be challenging. Let us handle it! If you send in your item or items - our team of specialists will test the item for you! If it happens to be gold-plated or not gold at all, we’ll simply send it back to you, for no charge',
			},
			{
				question: 'Do you accept Silver Utensils?',
				answer:
					'Though we do accept silver, we do not accept silver utensils. When it comes to silver, we accept Silver Coins, Silver Bars, Silver Bullion, Silver Rounds, and dimes/quarters/half dollars dated 1964 or older',
			},
		],
	},
	{
		id: 'what-sets-apart',
		title: '',
		items: [
			{
				question: 'Can I Trust Gold To Cash with handling my gold?',
				answer:
					'Absolutely. Gold to Cash is a secure and reputable online gold buyer, fully licensed under Washington State Chapter 19.60 RCW as a Secondhand Precious Metals Dealer. We operate in full compliance with state regulations and industry standards, ensuring that every transaction is handled professionally and responsibly. Over the years, thousands of customers have trusted Gold to Cash to sell their gold jewelry and precious metals, and many continue to recommend our service because of the reliability, transparency, and fair payouts we provide. Your valuables are protected throughout every stage of the transaction. From the moment your package is received, each item is carefully logged, handled by trained specialists, and safely stored. Our location is monitored and safeguarded by advanced security systems to ensure your items remain protected at all times. If you ever have questions about the process or the status of your shipment, our team is always available to help. To learn more about the experiences of other customers, we invite you to read verified customer reviews and see why so many people choose Gold to Cash when selling their gold.',
			},
			{
				question: 'What is the Price Match Guarantee?',
				answer:
					'Our goal is to make sure every customer feels confident and satisfied with the offer they receive. We believe selling your gold should be straightforward, transparent, and rewarding, which is why we work hard to provide competitive payouts based on current market prices. If you happen to receive a higher offer from another reputable online gold buyer, we’re happy to review it. In many cases, we can match a verified competitor’s offer so you can still take advantage of our secure process, fast payments, and trusted service while receiving the value you deserve.*Terms and conditions apply.',
			},
		],
	},
]

const visibleSections = computed(() => sectionBlocks.filter((b) => b.id === props.variant))

const flatFaqItems = computed(() =>
	visibleSections.value.flatMap((block) =>
		block.items.map((item) => ({
			question: item.question,
			answer: item.answer,
		}))
	)
)
</script>

<style scoped>
/* Gold calculator FAQ: gold band, accordion layout. */
.gc-faq {
	width: 100%;
	background: #c39e3d;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-faq__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-faq__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	align-self: stretch;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	text-align: center;
	letter-spacing: 0;
}

.gc-faq__page-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-style: normal;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	letter-spacing: 0;
	text-align: center;
	vertical-align: middle;
}

.gc-faq__page-title-part {
	font: inherit;
	letter-spacing: inherit;
}

.gc-faq__page-title-part--cream {
	color: #fff9ee;
}

.gc-faq__page-title-part--ink {
	color: #0b2230;
}

@media (min-width: 992px) {
	.gc-faq__page-title {
		font-size: 48px;
		line-height: 56px;
	}
}

.gc-faq__groups {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 40px;
	box-sizing: border-box;
}

/* Subsection labels — not in 62-735 wire; kept for content groups */
.gc-faq__block-title {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.25rem, 2.2vw, 1.875rem);
	line-height: 1.133;
	text-align: center;
	color: #000;
}

.gc-faq__list {
	display: flex;
	flex-direction: column;
	gap: 20px;
	width: 100%;
}

/* stroke_NJ5NKT 3px #000; collapsed: title bar fill #fff + question #000; open: title bar #000 + question #fff; answer: small + #000 on #fff */
.gc-faq__item {
	border: 3px solid #000;
	background: #fff;
	box-sizing: border-box;
	width: 100%;
	max-width: 1228px;
	margin: 0 auto;
}

.gc-faq__question {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	min-height: 80px;
	padding: 14px 16px;
	cursor: pointer;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.125rem, 2vw, 24px);
	line-height: 1;
	text-align: left;
	color: #000;
	background: #fff;
	list-style: none;
	box-sizing: border-box;
}

.gc-faq__question::-webkit-details-marker {
	display: none;
}

.gc-faq__item[open] .gc-faq__question {
	background: #000;
	color: #fff;
}

/* style_068AEC */
@media (min-width: 992px) {
	.gc-faq__question {
		font-size: 24px;
	}
}

.gc-faq__question-text {
	flex: 1;
}

/* Chevron — contrast with open/closed row */
.gc-faq__chev {
	flex-shrink: 0;
	width: 12px;
	height: 12px;
	border-right: 3px solid #000;
	border-bottom: 3px solid #000;
	transform: rotate(45deg);
	transition: transform 0.2s ease;
	margin-top: -6px;
}

.gc-faq__item[open] .gc-faq__chev {
	border-color: #fff;
	transform: rotate(-135deg);
	margin-top: 4px;
}

/* small: 500, 24px, 1.333 — answer on white */
.gc-faq__answer {
	padding: 14px 16px;
	background: #fff;
	box-sizing: border-box;
}

.gc-faq__answer p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 24px);
	line-height: 1.333;
	color: #000;
}

@media (min-width: 992px) {
	.gc-faq__answer p {
		font-size: 24px;
	}
}

@media (max-width: 991.98px) {
	.gc-faq {
		padding: 60px 0 80px;
	}

	.gc-faq__inner {
		gap: 32px;
	}
}

@media (max-width: 767.98px) {
	.gc-faq__page-title {
		font-size: 30px;
		line-height: 1.13;
	}
}
</style>
