<template>
	<section class="calc-reviews" aria-labelledby="calc-reviews-heading">
		<div class="calc-reviews__inner">
			<h2 id="calc-reviews-heading" class="calc-reviews__title">
				Customer Reviews On Trustpilot
			</h2>

			<div class="calc-reviews__row calc-reviews__row--desktop">
				<div class="calc-reviews__card calc-reviews__card--summary">
					<div class="calc-reviews__summary-calc">
						<p class="calc-reviews__score" aria-label="Rating 4.8 out of 5">4.8/5</p>
					</div>
					<div class="calc-reviews__summary-leave">
						<p class="calc-reviews__wordmark-text">Trustpilot</p>
						<div class="calc-reviews__tp-row">
							<div class="calc-reviews__avatars" aria-hidden="true">
								<span v-for="n in 4" :key="n" class="calc-reviews__avatar" />
							</div>
							<div class="calc-reviews__rev">
								<div class="calc-reviews__rev-stars-line">
									<TrustpilotFiveStarsSvg class="calc-reviews__five-stars calc-reviews__five-stars--compact" />
									<span class="calc-reviews__rating-small">4.8</span>
								</div>
								<p class="calc-reviews__trusted">
									Trusted by {{ TRUSTPILOT_HEADLINE_REVIEW_TOTAL }}+ people
								</p>
							</div>
						</div>
						<a
							class="calc-reviews__cta"
							href="https://www.trustpilot.com/review/goldtocash.us"
							target="_blank"
							rel="noopener noreferrer"
						>
							Leave a review
						</a>
					</div>
				</div>

				<article
					v-for="(t, i) in featured"
					:key="i"
					class="calc-reviews__card calc-reviews__card--review"
				>
					<div class="calc-reviews__review-body">
						<TrustpilotFiveStarsSvg class="calc-reviews__five-stars" />
						<p class="calc-reviews__text">
							<strong class="calc-reviews__text-lead">{{ formatReviewLead(t.title) }}</strong>
							{{ ' ' + t.review }}
						</p>
					</div>
					<div class="calc-reviews__meta">
						<span class="calc-reviews__author">{{ t.author }}</span>
					</div>
				</article>
			</div>

			<div class="calc-reviews__swiper-wrap">
				<Swiper
					class="calc-reviews-swiper"
					:modules="reviewsSwiperModules"
					:slides-per-view="1"
					:space-between="16"
					:loop="false"
					:pagination="{ clickable: true }"
				>
					<SwiperSlide>
						<div class="calc-reviews__card calc-reviews__card--summary calc-reviews__card--swiper">
							<div class="calc-reviews__summary-calc">
								<p class="calc-reviews__score" aria-label="Rating 4.8 out of 5">4.8/5</p>
							</div>
							<div class="calc-reviews__summary-leave">
								<p class="calc-reviews__wordmark-text">Trustpilot</p>
								<div class="calc-reviews__tp-row">
									<div class="calc-reviews__avatars" aria-hidden="true">
										<span v-for="n in 4" :key="n" class="calc-reviews__avatar" />
									</div>
									<div class="calc-reviews__rev">
										<div class="calc-reviews__rev-stars-line">
											<TrustpilotFiveStarsSvg class="calc-reviews__five-stars calc-reviews__five-stars--compact" />
											<span class="calc-reviews__rating-small">4.8</span>
										</div>
										<p class="calc-reviews__trusted">
											Trusted by {{ TRUSTPILOT_HEADLINE_REVIEW_TOTAL }}+ people
										</p>
									</div>
								</div>
								<a
									class="calc-reviews__cta"
									href="https://www.trustpilot.com/review/goldtocash.us"
									target="_blank"
									rel="noopener noreferrer"
								>
									Leave a review
								</a>
							</div>
						</div>
					</SwiperSlide>
					<SwiperSlide v-for="(t, i) in featured" :key="'m-' + i">
						<article class="calc-reviews__card calc-reviews__card--review calc-reviews__card--swiper">
							<div class="calc-reviews__review-body">
								<TrustpilotFiveStarsSvg class="calc-reviews__five-stars" />
								<p class="calc-reviews__text">
									<strong class="calc-reviews__text-lead">{{ formatReviewLead(t.title) }}</strong>
									{{ ' ' + t.review }}
								</p>
							</div>
							<div class="calc-reviews__meta">
								<span class="calc-reviews__author">{{ t.author }}</span>
							</div>
						</article>
					</SwiperSlide>
				</Swiper>
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
import TrustpilotFiveStarsSvg from '@/components/TrustpilotFiveStarsSvg.vue'
import {
	TRUSTPILOT_HEADLINE_REVIEW_TOTAL,
	trustpilotTestimonials
} from '@/constants/trustpilotTestimonials'

const reviewsSwiperModules = [Pagination]

const featured = computed(() => trustpilotTestimonials.slice(0, 3))

function formatReviewLead(title) {
	const s = title.trim()
	if (/[!?.]$/.test(s)) return s
	return `${s}.`
}
</script>

<style scoped>
.calc-reviews {
	padding: 100px 0;
	background: #000;
	width: 100%;
}

.calc-reviews__inner {
	width: 100%;
	max-width: 1320px;
	margin: 0 auto;
	padding-left: 24px;
	padding-right: 24px;
	box-sizing: border-box;
}

.calc-reviews__title {
	margin: 0 auto 30px;
	padding: 30px 0 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.5rem, 4vw, 3rem);
	line-height: 1.17;
	text-align: center;
	color: #fff;
}

.calc-reviews__row--desktop {
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 30px;
	align-items: stretch;
	width: 100%;
}

.calc-reviews__swiper-wrap {
	display: none;
	width: 100%;
}

.calc-reviews-swiper {
	width: 100%;
	overflow: hidden;
	padding-bottom: 4px;
}

.calc-reviews-swiper :deep(.swiper-slide) {
	height: auto;
	display: flex;
	align-items: stretch;
}

.calc-reviews-swiper :deep(.swiper-pagination) {
	position: static;
	display: flex;
	gap: 10px;
	align-items: center;
	justify-content: center;
	margin-top: 20px;
	min-height: 10px;
}

.calc-reviews-swiper :deep(.swiper-pagination-bullet) {
	width: 8px;
	height: 8px;
	border-radius: 50%;
	background: #666;
	opacity: 1;
	margin: 0 !important;
}

.calc-reviews-swiper :deep(.swiper-pagination-bullet-active) {
	background: #dbaf3e;
}

.calc-reviews__card {
	box-sizing: border-box;
	width: 100%;
	min-width: 0;
	min-height: 350px;
	padding: 18px;
	border-radius: 20px;
	border: 3px solid #000;
	box-shadow: 8px 8px 0 0 #dbaf3e;
	display: flex;
	flex-direction: column;
}

.calc-reviews__card--swiper {
	min-height: 0;
	justify-content: space-between;
}

.calc-reviews__card--summary {
	background: #fff9ee;
	justify-content: space-between;
	gap: 85px;
}

.calc-reviews__summary-calc {
	display: flex;
	flex-direction: column;
	gap: 12px;
	align-self: stretch;
}

.calc-reviews__score {
	margin: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: clamp(2.5rem, 4.5vw, 3.5rem);
	line-height: 1;
	letter-spacing: -0.06em;
	color: #000;
}

.calc-reviews__summary-leave {
	display: flex;
	flex-direction: column;
	align-self: stretch;
	gap: 16px;
	margin-top: auto;
}

.calc-reviews__wordmark-text {
	margin: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 14px;
	line-height: 1.2;
	color: #000;
}

.calc-reviews__tp-row {
	display: flex;
	flex-direction: row;
	align-items: flex-start;
	gap: 12px;
	flex-wrap: wrap;
}

.calc-reviews__avatars {
	display: flex;
	flex-direction: row;
	align-items: center;
}

.calc-reviews__avatar {
	width: 26px;
	height: 26px;
	border-radius: 50%;
	background: #dbaf3e;
	border: 2px solid #fff4d3;
	flex-shrink: 0;
	margin-left: -6px;
	box-sizing: border-box;
}

.calc-reviews__avatar:first-child {
	margin-left: 0;
}

.calc-reviews__rev {
	display: flex;
	flex-direction: column;
	gap: 4px;
	min-width: 0;
	flex: 1;
}

.calc-reviews__rev-stars-line {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: 6px;
	flex-wrap: wrap;
}

.calc-reviews__five-stars {
	width: 100%;
	max-width: 120px;
}

.calc-reviews__five-stars--compact {
	max-width: 88px;
}

.calc-reviews__rating-small {
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: 14px;
	line-height: 1.71;
	color: #000;
}

.calc-reviews__trusted {
	margin: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: 12px;
	line-height: 2;
	color: #000;
}

.calc-reviews__cta {
	display: flex;
	align-items: center;
	justify-content: center;
	align-self: stretch;
	min-height: 48px;
	padding: 24px 16px;
	border-radius: 8px;
	background: #000;
	color: #fff;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 16px;
	line-height: 1.25;
	text-align: center;
	text-decoration: none;
	transition: opacity 0.2s ease;
}

.calc-reviews__cta:hover {
	opacity: 0.88;
	color: #fff;
	text-decoration: none;
}

.calc-reviews__card--review {
	background: #fff;
	justify-content: space-between;
	gap: 24px;
}

.calc-reviews__review-body {
	display: flex;
	flex-direction: column;
	align-self: stretch;
	gap: 12px;
	min-height: 0;
}

.calc-reviews__text {
	margin: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: clamp(0.95rem, 1.2vw, 1.25rem);
	line-height: 1.4;
	text-align: left;
	color: #000;
}

.calc-reviews__text-lead {
	font-weight: 600;
}

.calc-reviews__meta {
	display: flex;
	justify-content: flex-start;
	align-items: flex-end;
	align-self: stretch;
	margin-top: auto;
}

.calc-reviews__author {
	font-family: Montserrat, sans-serif;
	font-weight: 700;
	font-size: 18px;
	line-height: 1.11;
	color: #000;
}

@media (max-width: 1199.98px) {
	.calc-reviews__row--desktop {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
}

@media (max-width: 767.98px) {
	.calc-reviews {
		padding: 60px 0 72px;
	}

	.calc-reviews__inner {
		padding-left: 16px;
		padding-right: 16px;
	}

	.calc-reviews__row--desktop {
		display: none;
	}

	.calc-reviews__swiper-wrap {
		display: block;
	}

	.calc-reviews__card--swiper {
		min-height: 0;
		aspect-ratio: 1;
		width: 100%;
		max-width: min(360px, 100%);
		margin-left: auto;
		margin-right: auto;
	}

	.calc-reviews__card--summary.calc-reviews__card--swiper {
		gap: 28px;
	}

	.calc-reviews__card--review.calc-reviews__card--swiper .calc-reviews__review-body {
		min-height: 0;
		flex: 1;
		overflow-y: auto;
		-webkit-overflow-scrolling: touch;
	}

	.calc-reviews__text {
		font-size: clamp(1rem, 3.5vw, 1.125rem);
	}
}
</style>
