<template>
	<section
		class="gc-figma-372-403"
		data-figma-node="372-403"
		aria-labelledby="gc-figma-372-403-heading"
	>
		<div class="gc-figma-372-403__inner">
			<header class="gc-figma-372-403__header">
				<h2 id="gc-figma-372-403-heading" class="gc-figma-372-403__title">Why Choose Gold To Cash</h2>
				<p class="gc-figma-372-403__intro">
					When you sell your scrap gold, you deserve honest pricing, real transparency, and fast payouts. That’s what
					we’re known for.
				</p>
			</header>

			<div class="gc-figma-372-403__form" aria-label="What sets us apart">
				<p class="gc-figma-372-403__label">What sets us apart</p>
				<ul class="gc-figma-372-403__cards gc-figma-372-403__cards--desktop" role="list">
					<li
						v-for="(card, i) in cards"
						:key="i"
						class="gc-figma-372-403__card"
						:class="card.shadow === 'gold' ? 'gc-figma-372-403__card--shadow-gold' : 'gc-figma-372-403__card--shadow-gray'"
					>
						<h3 class="gc-figma-372-403__card-title">{{ card.title }}</h3>
						<p class="gc-figma-372-403__card-text">{{ card.body }}</p>
					</li>
				</ul>

				<div class="gc-figma-372-403__swiper-wrap">
					<Swiper
						class="gc-figma-372-403-swiper"
						:modules="chooseSwiperModules"
						:slides-per-view="1"
						:space-between="16"
						:loop="false"
						:pagination="{ clickable: true }"
					>
						<SwiperSlide v-for="(card, i) in cards" :key="'choose-' + i">
							<article
								class="gc-figma-372-403__card gc-figma-372-403__card--swiper"
								:class="card.shadow === 'gold' ? 'gc-figma-372-403__card--shadow-gold' : 'gc-figma-372-403__card--shadow-gray'"
							>
								<h3 class="gc-figma-372-403__card-title">{{ card.title }}</h3>
								<p class="gc-figma-372-403__card-text">{{ card.body }}</p>
							</article>
						</SwiperSlide>
					</Swiper>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

const chooseSwiperModules = [Pagination]

const props = defineProps({
	/** Dental calculator page uses alternate card copy; scrap unchanged. */
	pageVariant: {
		type: String,
		default: 'scrap',
		validator: (v) => ['scrap', 'dental', 'calculator'].includes(v),
	},
})

const SCRAP_CARDS = [
	{
		title: 'Free insured mail-in kits',
		body: 'safe, simple, and risk-free',
		shadow: 'gold',
	},
	{
		title: 'Highest market payouts',
		body: 'calculated from current spot prices, not flat rates',
		shadow: 'gray',
	},
	{
		title: 'Professional testing',
		body: 'every item is weighed and tested for purity by certified experts',
		shadow: 'gold',
	},
	{
		title: 'Lightning-fast offers',
		body: 'get your quote within 24 hours of delivery',
		shadow: 'gray',
	},
	{
		title: 'Trusted nationwide',
		body: 'thousands of satisfied customers across the U.S.',
		shadow: 'gold',
	},
]

const DENTAL_CARDS = [
	{
		title: 'Free insured mail-in kits',
		body: 'no cost, no risk.',
		shadow: 'gold',
	},
	{
		title: 'Top market payouts',
		body: 'we pay based on current gold values, not flat estimates.',
		shadow: 'gray',
	},
	{
		title: 'Fast turnaround',
		body: 'get your offer within 24 hours of delivery.',
		shadow: 'gold',
	},
	{
		title: 'Transparent process',
		body: 'we show you exactly how your items are evaluated and weighed.',
		shadow: 'gray',
	},
	{
		title: 'Trusted buyers',
		body: 'thousands of satisfied customers across the U.S.',
		shadow: 'gold',
	},
]

const cards = computed(() => (props.pageVariant === 'dental' ? DENTAL_CARDS : SCRAP_CARDS))
</script>

<style scoped>
/* “Why choose us” band + feature cards (scrap vs dental copy via pageVariant). */
.gc-figma-372-403 {
	width: 100%;
	background: rgba(195, 158, 61, 0.3);
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-figma-372-403__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 50px;
}

.gc-figma-372-403__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	align-self: stretch;
	gap: 20px;
	padding: 30px 0;
	box-sizing: border-box;
	text-align: center;
}

.gc-figma-372-403__title {
	margin: 0;
	max-width: 1227px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #000;
}

.gc-figma-372-403__intro {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.333;
	color: #303030;
}

.gc-figma-372-403__form {
	position: relative;
	width: 100%;
	max-width: 1536px;
	min-height: 0;
}

.gc-figma-372-403__label {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.333;
	text-align: center;
	color: #c39e3d;
}

.gc-figma-372-403__cards--desktop {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: stretch;
	gap: 30px;
	box-sizing: border-box;
}

/* Five cards: three on the first row; second row centers the remaining pair. */
@media (min-width: 768px) {
	.gc-figma-372-403__cards--desktop {
		display: grid;
		grid-template-columns: repeat(12, minmax(0, 1fr));
		align-items: stretch;
	}

	.gc-figma-372-403__cards--desktop > .gc-figma-372-403__card:nth-child(-n + 3) {
		grid-column: span 4;
		max-width: none;
		width: auto;
		min-width: 0;
	}

	.gc-figma-372-403__cards--desktop > .gc-figma-372-403__card:nth-child(4) {
		grid-column: 3 / span 4;
		max-width: none;
		width: auto;
		min-width: 0;
	}

	.gc-figma-372-403__cards--desktop > .gc-figma-372-403__card:nth-child(5) {
		grid-column: 7 / span 4;
		max-width: none;
		width: auto;
		min-width: 0;
	}
}

.gc-figma-372-403__swiper-wrap {
	display: none;
	width: 100%;
}

.gc-figma-372-403-swiper {
	width: 100%;
	overflow: hidden;
	padding-bottom: 4px;
}

.gc-figma-372-403-swiper :deep(.swiper-slide) {
	height: auto;
	display: flex;
	align-items: flex-start;
	justify-content: center;
}

.gc-figma-372-403-swiper :deep(.swiper-pagination) {
	position: static;
	display: flex;
	gap: 10px;
	align-items: center;
	justify-content: center;
	margin-top: 20px;
	min-height: 10px;
}

.gc-figma-372-403-swiper :deep(.swiper-pagination-bullet) {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #666;
	opacity: 1;
	margin: 0 !important;
}

.gc-figma-372-403-swiper :deep(.swiper-pagination-bullet-active) {
	background: #dbaf3e;
}

.gc-figma-372-403__card {
	width: 100%;
	max-width: 283px;
	min-height: 350px;
	box-sizing: border-box;
	padding: 40px 18px;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 40px;
	background: #fff;
	border: 3px solid #000;
	border-radius: 20px;
}

.gc-figma-372-403__card--shadow-gold {
	box-shadow: -12px 12px 0 0 #c39e3d;
}

.gc-figma-372-403__card--shadow-gray {
	box-shadow: -12px 12px 0 0 rgba(0, 0, 0, 0.3);
}

.gc-figma-372-403__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.125rem, 2.2vw, 30px);
	line-height: 1.133;
	color: #000;
}

.gc-figma-372-403__card-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(0.9375rem, 1.8vw, 24px);
	line-height: 1.333;
	color: #000;
}

@media (min-width: 992px) {
	.gc-figma-372-403__title {
		font-size: 48px;
		line-height: 56px;
	}
}

@media (max-width: 991.98px) {
	.gc-figma-372-403 {
		padding: 60px 0 80px;
	}

	.gc-figma-372-403__inner {
		gap: 40px;
	}

	.gc-figma-372-403__card {
		max-width: 100%;
		min-height: 0;
	}
}

@media (max-width: 767.98px) {
	.gc-figma-372-403__inner {
		padding: 0 16px;
	}

	.gc-figma-372-403__header {
		padding: 16px 0;
	}

	.gc-figma-372-403__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-372-403__intro {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-figma-372-403__label {
		font-size: 24px;
		line-height: 1.25;
		margin-bottom: 16px;
	}

	.gc-figma-372-403__cards--desktop {
		display: none;
	}

	.gc-figma-372-403__swiper-wrap {
		display: block;
	}

	.gc-figma-372-403__card {
		min-height: 0;
		padding: 20px 16px;
		gap: 14px;
	}

	.gc-figma-372-403__card--swiper {
		width: min(100%, 280px);
		max-width: 280px;
		aspect-ratio: 1;
		min-width: 0;
		min-height: 0;
		margin: 0 auto;
		box-sizing: border-box;
		align-self: center;
		justify-content: center;
	}

	.gc-figma-372-403__card-title {
		font-size: clamp(1.3125rem, 5vw, 1.5rem);
		line-height: 1.2;
	}

	.gc-figma-372-403__card-text {
		font-size: clamp(1.0625rem, 4.2vw, 1.25rem);
		line-height: 1.4;
	}

	.gc-figma-372-403__card--shadow-gold {
		box-shadow: -8px 8px 0 0 #c39e3d;
	}

	.gc-figma-372-403__card--shadow-gray {
		box-shadow: -8px 8px 0 0 rgba(0, 0, 0, 0.3);
	}
}
</style>
