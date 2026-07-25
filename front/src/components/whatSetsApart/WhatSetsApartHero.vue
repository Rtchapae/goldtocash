<template>
	<section class="wsa-hero" aria-labelledby="wsa-hero-title">
		<div class="wsa-hero__inner">
			<header class="wsa-hero__header">
				<h1 id="wsa-hero-title" class="wsa-hero__title">What Sets Us Apart</h1>
				<p class="wsa-hero__subtitle">Gold Standard Service</p>
			</header>

			<ul class="wsa-hero__cards" role="list">
				<li v-for="(card, i) in cards" :key="i" class="wsa-hero__card">
					<img
						class="wsa-hero__icon"
						:src="card.iconSrc"
						alt=""
						width="92"
						height="92"
						loading="eager"
						decoding="async"
					/>
					<div class="wsa-hero__copy">
						<h2 class="wsa-hero__card-title">{{ card.title }}</h2>
						<p class="wsa-hero__card-text">{{ card.text }}</p>
					</div>
				</li>
			</ul>

			<a
				class="wsa-hero__cta"
				href="/#getStartedForm"
				@click.prevent="openKitModal && openKitModal()"
			>Get a free estimate</a>
		</div>
	</section>
</template>

<script setup>
import { inject } from 'vue'

const openKitModal = inject('openKitModal', null)

/** Figma 694:3567 — section.calc_pay hero */
const cards = [
	{
		title: 'Free\nShipping',
		text: 'Our mail-kits are safe, simple, insured, and risk-free',
		iconSrc: '/images/what-sets-apart/icon-shipping.png?v=3',
	},
	{
		title: 'Lightning-fast\noffers',
		text: 'get your quote within 24 hours of delivery',
		iconSrc: '/images/what-sets-apart/icon-flash.png?v=3',
	},
	{
		title: 'Professional\nTesting',
		text: 'every item is weighed and tested for purity by certified experts.',
		iconSrc: '/images/what-sets-apart/icon-scales.png?v=3',
	},
]
</script>

<style scoped>
/* Figma 694:3567 — white section, gold title over black subtitle, bare columns, black CTA */
.wsa-hero {
	width: 100%;
	background: #fff;
	/* Navbar clearance + Figma section pad (~50) */
	padding: 150px 0 50px;
	box-sizing: border-box;
}

.wsa-hero__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 80px;
}

.wsa-hero__header {
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	width: 100%;
}

.wsa-hero__title {
	margin: 0;
	padding: 30px 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #dbaf3e;
	text-align: center;
}

.wsa-hero__subtitle {
	margin: 0;
	padding: 30px 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #000;
	text-align: center;
}

.wsa-hero__cards {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	max-width: 1230px;
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 390px));
	justify-content: center;
	gap: 30px;
}

.wsa-hero__card {
	margin: 0;
	min-height: 302px;
	padding: 0;
	background: transparent;
	border: none;
	border-radius: 0;
	box-shadow: none;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	gap: 30px;
	text-align: center;
}

.wsa-hero__icon {
	display: block;
	width: 92px;
	height: 92px;
	object-fit: contain;
	flex-shrink: 0;
}

.wsa-hero__copy {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 10px;
	max-width: 212px;
}

.wsa-hero__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 30px;
	line-height: 34px;
	color: #000;
	white-space: pre-line;
	text-align: center;
}

.wsa-hero__card-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #000;
	text-align: center;
}

.wsa-hero__cta {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	min-width: 421px;
	min-height: 72px;
	padding: 24px;
	box-sizing: border-box;
	background: #000;
	color: #fff;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 20px;
	line-height: 24px;
	text-decoration: none;
	border: none;
	border-radius: 8px;
	box-shadow: none;
}

.wsa-hero__cta:hover {
	background: #1a1a1a;
	color: #fff;
}

@media (max-width: 991.98px) {
	.wsa-hero {
		padding: 140px 0 48px;
	}

	.wsa-hero__inner {
		gap: 48px;
	}

	.wsa-hero__title,
	.wsa-hero__subtitle {
		padding: 12px 0;
		line-height: 1.2;
	}

	.wsa-hero__cards {
		grid-template-columns: 1fr;
		gap: 40px;
		max-width: 390px;
	}

	.wsa-hero__card {
		min-height: 0;
	}

	.wsa-hero__cta {
		min-width: 0;
		width: 100%;
		max-width: 421px;
	}
}
</style>
