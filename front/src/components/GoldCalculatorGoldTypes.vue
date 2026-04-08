<template>
	<section
		id="gold-calculator-figma-62-830"
		class="gc-gold-types"
		aria-labelledby="gc-gold-types-heading"
		data-figma-node="62-830"
	>
		<div class="gc-gold-types__inner">
			<header class="gc-gold-types__header">
				<h2 id="gc-gold-types-heading" class="gc-gold-types__title">
					Types Of Gold You Can Calculate
				</h2>
				<p class="gc-gold-types__subtitle">
					Gold To Cash’s calculator supports a wide range of gold items, including:
				</p>
			</header>

			<ul class="gc-gold-types__grid gc-gold-types__grid--desktop">
				<li v-for="(card, i) in cards" :key="i" class="gc-gold-types__card">
					<div
						class="gc-gold-types__media"
						:style="{ backgroundImage: `url(${card.image})` }"
						aria-hidden="true"
					/>
					<div class="gc-gold-types__body">
						<h3 class="gc-gold-types__card-title">{{ card.title }}</h3>
						<p class="gc-gold-types__card-text">{{ card.text }}</p>
					</div>
				</li>
			</ul>

			<div class="gc-gold-types__swiper-wrap">
				<Swiper
					class="gc-gold-types-swiper"
					:modules="goldTypesSwiperModules"
					:slides-per-view="1"
					:space-between="16"
					:loop="false"
					:pagination="{ clickable: true }"
				>
					<SwiperSlide v-for="(card, i) in cards" :key="'s-' + i">
						<div class="gc-gold-types__card gc-gold-types__card--slide">
							<div
								class="gc-gold-types__media"
								:style="{ backgroundImage: `url(${card.image})` }"
								aria-hidden="true"
							/>
							<div class="gc-gold-types__body">
								<h3 class="gc-gold-types__card-title">{{ card.title }}</h3>
								<p class="gc-gold-types__card-text">{{ card.text }}</p>
							</div>
						</div>
					</SwiperSlide>
				</Swiper>
			</div>

			<p class="gc-gold-types__footnote">
				No matter the form, if it contains real gold, we can calculate it.
			</p>
		</div>
	</section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

const goldTypesSwiperModules = [Pagination]

const cards = [
	{
		title: 'Jewelry',
		text: 'Rings, necklaces, bracelets, earrings, and pendants',
		image: '/images/how-sell-gold-jewelry-1.png'
	},
	{
		title: 'Coins & Bullion',
		text: 'Investment-grade gold',
		image: '/images/gold-buyer-online-1.png'
	},
	{
		title: 'Scrap Gold',
		text: 'Broken or mismatched pieces',
		image: '/images/best-place-sell-gold-1.png'
	},
	{
		title: 'Dental Gold',
		text: 'Crowns, bridges, and fillings (even with porcelain or enamel attached)',
		image: '/images/how-sell-gold-jewelry-2.png'
	},
	{
		title: 'Gold Nuggets or Bars',
		text: 'Raw gold in bars, nuggets, or other bullion forms',
		image: '/images/gold-buyer-online-2.png'
	}
]
</script>

<style scoped>
.gc-gold-types {
	width: 100%;
	background: #fbf5e8;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-gold-types__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-gold-types__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	text-align: center;
}

.gc-gold-types__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.17;
	color: #000;
}

.gc-gold-types__subtitle {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1.125rem, 2vw, 1.5rem);
	line-height: 1.33;
	color: #dbaf3e;
	max-width: 1226px;
}

.gc-gold-types__grid {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
	gap: 20px;
	box-sizing: border-box;
}

.gc-gold-types__swiper-wrap {
	display: none;
	width: 100%;
	max-width: 400px;
	margin: 0 auto;
}

.gc-gold-types-swiper {
	width: 100%;
	overflow: hidden;
	padding-bottom: 8px;
}

.gc-gold-types-swiper :deep(.swiper-slide) {
	height: auto;
}

.gc-gold-types-swiper :deep(.swiper-pagination) {
	position: static;
	display: flex;
	gap: 8px;
	justify-content: center;
	margin-top: 16px;
	min-height: 10px;
}

.gc-gold-types-swiper :deep(.swiper-pagination-bullet) {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #c39e3d;
	opacity: 0.35;
	margin: 0 !important;
}

.gc-gold-types-swiper :deep(.swiper-pagination-bullet-active) {
	opacity: 1;
	background: #c39e3d;
}

.gc-gold-types__card--slide {
	width: 100%;
	max-width: 360px;
	margin: 0 auto;
	min-height: 320px;
}

.gc-gold-types__card {
	position: relative;
	display: flex;
	flex-direction: column;
	width: 230px;
	min-height: 300px;
	border-radius: 12px;
	background: #f5f4f1;
	overflow: hidden;
	box-sizing: border-box;
	box-shadow: 0 5px 24px rgba(0, 0, 0, 0.08);
}

.gc-gold-types__media {
	position: absolute;
	inset: 0;
	background-size: cover;
	background-position: center;
	background-repeat: no-repeat;
}

.gc-gold-types__body {
	position: relative;
	z-index: 1;
	margin-top: auto;
	min-height: 42%;
	padding: 18px 18px 22px;
	background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.55) 28%, rgba(0, 0, 0, 0.82) 100%);
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
	gap: 10px;
	box-sizing: border-box;
}

.gc-gold-types__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.125rem, 2.2vw, 1.625rem);
	line-height: 1.23;
	color: #fff;
}

.gc-gold-types__card-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(0.8125rem, 1.4vw, 1.125rem);
	line-height: 1.25;
	color: #fff;
}

.gc-gold-types__footnote {
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

@media (max-width: 1279.98px) {
	.gc-gold-types__card {
		width: calc(33.333% - 14px);
		min-width: 200px;
		max-width: 280px;
	}
}

@media (max-width: 991.98px) {
	.gc-gold-types {
		padding: 60px 0 80px;
	}

	.gc-gold-types__card {
		width: calc(50% - 10px);
		max-width: none;
	}
}

@media (max-width: 539.98px) {
	.gc-gold-types__card:not(.gc-gold-types__card--slide) {
		width: 100%;
		max-width: 360px;
		min-height: 280px;
	}
}

@media (max-width: 767.98px) {
	.gc-gold-types__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-gold-types__subtitle {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-gold-types__footnote {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-gold-types__grid--desktop {
		display: none;
	}

	.gc-gold-types__swiper-wrap {
		display: block;
	}

	.gc-gold-types__card.gc-gold-types__card--slide {
		width: 100%;
		max-width: min(360px, 92vw);
		min-height: 0;
		aspect-ratio: 1;
		margin-left: auto;
		margin-right: auto;
	}
}
</style>
