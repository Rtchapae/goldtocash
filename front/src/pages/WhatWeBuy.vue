<template>
	<div class="what-we-buy-page">
		<WhatWeBuyCategories />
		<WhatWeBuyProcess />
		<WhatWeBuyTrust />
		<GoldCalculatorFaq variant="what-we-buy" />
		<section class="wwb-cta" aria-labelledby="wwb-cta-title">
			<div class="wwb-cta__inner">
				<div class="wwb-cta__copy">
					<h2 id="wwb-cta-title" class="wwb-cta__title">Unlock The Value of Your Gold Today</h2>
					<p class="wwb-cta__p">
						Whether it’s broken jewelry, old pieces you no longer wear, or forgotten keepsakes sitting in a drawer,
						your gold still holds real value. With gold prices remaining strong in today’s market, now may be a great
						time to take advantage of that value and turn unused items into extra cash.
					</p>
					<p class="wwb-cta__p">
						If you’d like to join the thousands of customers who have already trusted Gold to Cash, you can start by
						getting a quick estimate using our Scrap Gold Calculator. From there, simply fill out the short form to
						request your free mail-in appraisal kit. Our team will professionally evaluate your gold and provide a
						competitive offer with fast payment. The entire process is simple, secure, and completely risk-free.
					</p>
				</div>
				<figure class="wwb-cta__figure">
					<img
						class="wwb-cta__img"
						src="/images/what-we-buy/cta-hero.webp"
						width="647"
						height="440"
						loading="lazy"
						decoding="async"
						alt="Gold jewelry ready to unlock its value"
					/>
				</figure>
			</div>
		</section>
	</div>
</template>

<script setup>
import { onMounted } from 'vue'
import WhatWeBuyCategories from '@/components/whatWeBuy/WhatWeBuyCategories.vue'
import WhatWeBuyProcess from '@/components/whatWeBuy/WhatWeBuyProcess.vue'
import WhatWeBuyTrust from '@/components/whatWeBuy/WhatWeBuyTrust.vue'
import GoldCalculatorFaq from '@/components/GoldCalculatorFaq.vue'
import seoService from '@/services/seoService.js'

onMounted(() => {
	seoService.setMeta({
		title: 'What We Buy - Gold & Silver Jewelry | Gold To Cash',
		description:
			'See what gold and silver items we buy: rings, necklaces, watches, bars, dentures, and more. Free appraisal kit, fair pricing, fast payment.',
	})
})
</script>

<style scoped>
.wwb-cta {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.wwb-cta__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	gap: 48px;
	box-sizing: border-box;
}

.wwb-cta__copy {
	flex: 1 1 560px;
	min-width: 0;
}

.wwb-cta__title {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.2vw, 36px);
	line-height: 1.2;
	color: #000;
}

.wwb-cta__p {
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 16px;
	line-height: 1.5;
	color: #000;
}

.wwb-cta__figure {
	flex: 0 0 auto;
	width: min(604px, 48%);
	max-width: 604px;
	margin: 0;
	box-shadow:
		10px 10px 0 0 #c39e3d,
		10px 10px 4px 0 rgba(0, 0, 0, 0.25);
}

.wwb-cta__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 647 / 440;
	object-fit: cover;
}

@media (max-width: 991.98px) {
	.wwb-cta {
		padding: 64px 0;
	}

	.wwb-cta__inner {
		flex-direction: column;
		align-items: stretch;
	}

	.wwb-cta__figure {
		width: 100%;
		max-width: none;
	}
}
</style>
