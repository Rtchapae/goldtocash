<template>
	<section
		id="gold-calculator-figma-38-2684"
		class="gc-why-use"
		aria-labelledby="gc-why-use-heading"
		data-figma-node="38-2684"
	>
		<div class="gc-why-use__inner">
			<header class="gc-why-use__header">
				<h2 id="gc-why-use-heading" class="gc-why-use__title">
					Why Use Gold To Cash’s Calculator?
				</h2>
			</header>

			<div class="gc-why-use__cards">
				<article
					v-for="(card, i) in cards"
					:key="i"
					class="gc-why-use__card"
					:class="card.variant === 'dark' ? 'gc-why-use__card--shadow-dark' : 'gc-why-use__card--shadow-gold'"
				>
					<h3 class="gc-why-use__card-title">{{ card.title }}</h3>
					<p class="gc-why-use__card-text">{{ card.text }}</p>
				</article>
			</div>

			<p class="gc-why-use__outro">
				We believe in complete transparency. Our goal is to help you get the true value of your gold with zero
				guesswork.
			</p>
		</div>
	</section>
</template>

<script setup>
const cards = [
	{
		title: 'Real-Time Market Pricing',
		text: 'Updated automatically from current gold rates',
		variant: 'gold'
	},
	{
		title: 'Simple & Secure',
		text: 'No sign-up required - instant estimate in seconds',
		variant: 'dark'
	},
	{
		title: 'Specialized Tools',
		text: 'Dental, scrap, and melt calculators for precise valuation',
		variant: 'gold'
	},
	{
		title: 'Trusted Process',
		text: 'Free insured kits, no hidden fees, and fast payouts',
		variant: 'dark'
	}
]
</script>

<style scoped>
.gc-why-use {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-why-use__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-why-use__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	text-align: center;
}

.gc-why-use__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.167;
	color: #dbaf3e;
}

.gc-why-use__cards {
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
	align-items: stretch;
	gap: 30px;
	width: 100%;
	list-style: none;
	margin: 0;
	padding: 0;
}

.gc-why-use__card {
	width: 100%;
	max-width: 283px;
	min-height: 350px;
	padding: 40px 18px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	gap: 40px;
	background: #fff;
	border: 3px solid #000;
	border-radius: 20px;
}

.gc-why-use__card--shadow-gold {
	box-shadow: -12px 12px 0 0 #c39e3d;
}

.gc-why-use__card--shadow-dark {
	box-shadow: -12px 12px 0 0 rgba(0, 0, 0, 0.3);
}

.gc-why-use__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.25rem, 2.2vw, 1.875rem);
	line-height: 1.133;
	text-align: left;
	color: #000;
}

.gc-why-use__card-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 1.5rem);
	line-height: 1.333;
	text-align: left;
	color: #000;
}

.gc-why-use__outro {
	margin: 0;
	max-width: 1228px;
	width: 100%;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	text-align: center;
	color: #000;
}

@media (max-width: 991.98px) {
	.gc-why-use {
		padding: 60px 0 80px;
	}

	.gc-why-use__card {
		max-width: 100%;
		min-height: 0;
	}
}

@media (min-width: 576px) and (max-width: 991.98px) {
	.gc-why-use__card {
		max-width: calc(50% - 15px);
		flex: 1 1 calc(50% - 15px);
	}
}
</style>
