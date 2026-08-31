<template>
	<section class="wwp-factors" aria-labelledby="wwp-factors-heading" id="spot-pricing">
		<div class="wwp-factors__inner">
			<article
				v-for="block in blocks"
				:key="block.num"
				class="wwp-factors__block"
				:class="`wwp-factors__block--${block.num}`"
			>
				<p class="wwp-factors__num" aria-hidden="true">{{ block.num }}</p>
				<h3 :id="block.num === '01' ? 'wwp-factors-heading' : undefined" class="wwp-factors__title">
					{{ block.title }}
				</h3>
				<p class="wwp-factors__text">{{ block.body }}</p>
				<ul v-if="block.purities" class="wwp-factors__purities" role="list">
					<li v-for="(row, i) in block.purities" :key="i">{{ row }}</li>
				</ul>
			</article>

			<aside class="wwp-factors__card" aria-label="Current spot prices">
				<p class="wwp-factors__card-title">Current Spot Prices</p>
				<ul class="wwp-factors__prices" role="list">
					<li v-for="row in priceRows" :key="row.label" class="wwp-factors__price-row">
						<span>{{ row.label }}</span>
						<span>{{ row.value }}</span>
					</li>
				</ul>
				<p class="wwp-factors__card-note">Prices updated every minute via live market data.</p>
			</aside>
		</div>
	</section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { getCurrentGoldPrice } from '@/api/calculator'

/** Figma 1022:3497 / 1020:3069 — pricing factors + spot card */
const TROY_OZ_GRAMS = 31.1034768
const PENNYWEIGHT_PER_TROY = 20

const spotOzt = ref(null)
const loadError = ref(false)

const formatUsd = (n) =>
	new Intl.NumberFormat('en-US', {
		style: 'currency',
		currency: 'USD',
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(n)

const priceRows = computed(() => {
	if (loadError.value || spotOzt.value == null) {
		const dash = loadError.value ? '—' : '…'
		return [
			{ label: 'Per gram', value: dash },
			{ label: 'Per pennyweight', value: dash },
			{ label: 'Per troy ounce', value: dash },
		]
	}
	const ozt = spotOzt.value
	return [
		{ label: 'Per gram', value: formatUsd(ozt / TROY_OZ_GRAMS) },
		{ label: 'Per pennyweight', value: formatUsd(ozt / PENNYWEIGHT_PER_TROY) },
		{ label: 'Per troy ounce', value: formatUsd(ozt) },
	]
})

const blocks = [
	{
		num: '01',
		title: 'Current Market Pricing:',
		body:
			'Out of the 3, the current market price for gold/silver (or Spot Price) is the most important factor to determine value of your jewelry. Gold to Cash determines gold & silver pricing using industry-standard precious metal pricing services and monitors the market price by the hour. This allows us to give you the most accurate offers possible. Check out the latest Spot Pricing for Gold & Silver below.',
	},
	{
		num: '02',
		title: 'Weight',
		body:
			'The next factor is weight. Naturally, more precious metal contained in your jewelry yields a higher cash value. We weigh each individual piece of jewelry that you send, down to the gram. But don’t be fooled, if your jewelry is heavy, that does not mean it’s filled with a lot of gold. Your jewelry can, and in most cases does, contain mixed with different alloys like silver, zinc, copper etc. When this happens, it changes the purity of the jewelry. Which brings us to our final factor...',
	},
	{
		num: '03',
		title: 'Purity',
		body:
			'Purity is the last factor to determine what Gold to Cash pays for your jewelry. Depending on how pure your gold is, that determines the tier or carat (“ct” or “k”). The most common gold purities are:',
		purities: [
			'24 carat: 99+% Pure Gold',
			'22 carat: 91.6% Pure Gold',
			'18 carat: 75.0% Pure Gold',
			'14 carat: 58.3% Pure Gold',
			'10 carat: 41.7% Pure Gold',
		],
	},
]

onMounted(async () => {
	try {
		const data = await getCurrentGoldPrice()
		spotOzt.value = data.numeric
	} catch {
		loadError.value = true
	}
})
</script>

<style scoped>
.wwp-factors {
	width: 100%;
	background: #fff4d3;
	padding: 80px 0 100px;
	box-sizing: border-box;
}

.wwp-factors__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(360px, 440px);
	grid-template-areas:
		'b01 .'
		'b02 card'
		'b03 .';
	column-gap: 40px;
	row-gap: 48px;
	align-items: start;
}

.wwp-factors__block {
	display: flex;
	flex-direction: column;
	gap: 12px;
	align-items: center;
	width: 100%;
	text-align: center;
	min-width: 0;
}

.wwp-factors__block--01 {
	grid-area: b01;
}

.wwp-factors__block--02 {
	grid-area: b02;
}

.wwp-factors__block--03 {
	grid-area: b03;
}

.wwp-factors__num {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 18px;
	line-height: 1;
	text-align: center;
	color: #c39e3d;
}

.wwp-factors__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.5rem, 2.5vw, 32px);
	line-height: 1.2;
	text-align: center;
	color: #000;
}

.wwp-factors__text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 18px);
	line-height: 1.5;
	text-align: center;
	color: #000;
}

.wwp-factors__purities {
	list-style: none;
	margin: 8px 0 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 6px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 16px;
	line-height: 1.4;
	text-align: center;
	color: #000;
}

.wwp-factors__purities li {
	text-align: center;
}

.wwp-factors__card {
	grid-area: card;
	align-self: start;
	width: 100%;
	background: #fff;
	border-radius: 16px;
	padding: 28px 28px;
	box-shadow: 0 10px 32px rgba(0, 0, 0, 0.08);
	box-sizing: border-box;
}

.wwp-factors__card-title {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 22px;
	line-height: 1.2;
	text-align: center;
	color: #000;
}

.wwp-factors__prices {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 10px;
}

.wwp-factors__price-row {
	display: flex;
	justify-content: space-between;
	gap: 16px;
	padding: 14px 16px;
	background: rgba(195, 158, 61, 0.1); /* #C39E3D @ 10% — Figma 1022:3506 */
	border-radius: 8px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 15px;
	color: #000;
}

.wwp-factors__card-note {
	margin: 16px 0 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 12px;
	line-height: 1.35;
	color: #888;
}

@media (max-width: 991.98px) {
	.wwp-factors {
		padding: 56px 0 64px;
	}

	.wwp-factors__inner {
		grid-template-columns: 1fr;
		grid-template-areas:
			'b01'
			'b02'
			'b03'
			'card';
		row-gap: 36px;
		column-gap: 0;
	}
}
</style>
