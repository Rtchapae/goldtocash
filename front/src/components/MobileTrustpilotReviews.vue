<template>
	<!-- Figma 2680-1981: same mobile Trustpilot carousel as home (MobileSellReasons) -->
	<div class="reviews-block">
		<svg class="reviews-block-icon" width="39" height="36" viewBox="0 0 39 36" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M19.0189 27.4528L27.283 25.3585L30.7359 36L19.0189 27.4528ZM38.0377 13.6981H23.4906L19.0189 0L14.5472 13.6981H0L11.7736 22.1887L7.30188 35.8868L19.0755 27.3962L26.3207 22.1887L38.0377 13.6981Z" fill="#11BA69"/>
		</svg>
		<h2 class="reviews-block-heading">
			<span class="reviews-count">{{ trustpilotReviewsCountDisplay }}</span> positive<br>
			reviews on <span class="reviews-block-heading-accent">Trustpilot</span>
		</h2>
		<Swiper
			class="reviews-swiper"
			:modules="reviewsSwiperModules"
			:loop="false"
			:slides-per-view="1"
			:space-between="16"
			:centered-slides="true"
			:pagination="{ clickable: true }"
			@swiper="onReviewsSwiperInit"
			@slideChange="onReviewsSlideChange"
		>
			<SwiperSlide v-for="(t, i) in testimonials" :key="i">
				<div class="reviews-block-card">
					<img src="/images/trustpilot-stars-green.svg" alt="" class="reviews-card-stars" width="107" height="20" />
					<p class="reviews-card-title">{{ t.title }}</p>
					<p class="reviews-card-text">{{ t.review }}</p>
					<div class="reviews-card-line"></div>
					<p class="reviews-card-username">{{ t.author }}</p>
				</div>
			</SwiperSlide>
		</Swiper>
	</div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'
import {
	TRUSTPILOT_HEADLINE_REVIEW_TOTAL,
	trustpilotTestimonials as testimonials
} from '@/constants/trustpilotTestimonials'

const reviewsSwiperModules = [Pagination]
const reviewsActiveIndex = ref(0)
const reviewsSwiper = ref(null)

const trustpilotReviewsCountDisplay = computed(() => `${TRUSTPILOT_HEADLINE_REVIEW_TOTAL}+`)

const onReviewsSwiperInit = (swiper) => {
	reviewsSwiper.value = swiper
	reviewsActiveIndex.value = swiper.activeIndex ?? 0
}

const onReviewsSlideChange = (swiper) => {
	reviewsActiveIndex.value = swiper.activeIndex ?? 0
}
</script>
