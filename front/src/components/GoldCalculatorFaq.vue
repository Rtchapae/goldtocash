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
					<span class="gc-faq__page-title-part gc-faq__page-title-part--ink"> About Gold Value</span>
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
		validator: (v) => ['calculator', 'scrap', 'dental'].includes(v),
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
