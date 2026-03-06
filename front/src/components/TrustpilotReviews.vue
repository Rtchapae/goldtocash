<template>
	<section class="trustpilot-reviews">
		<div class="trustpilot-container">
			<h1 class="trustpilot-header">
				Customer Reviews on <span style="color:var(--primary)">Trustpilot</span>
			</h1>
			<div class="d-flex flex-wrap">
				<div v-if="isLoading" class="text-center w-100 py-5">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading reviews...</span>
					</div>
					<p class="mt-3">Loading customer reviews...</p>
				</div>

				<Swiper
					v-else-if="reviews.length > 0"
					class="trustpilot-swiper"
					:modules="modules"
					:loop="reviews.length >= 4"
					:slides-per-view="4"
					:slides-per-group="1"
					:looped-slides="Math.min(reviews.length, 4)"
					:space-between="15"
					:centered-slides="false"
					:navigation="navigation"
					:pagination="pagination"
					:autoplay="reviews.length >= 4 ? { delay: 4000, disableOnInteraction: true } : false"
					:grab-cursor="true"
					:watch-overflow="false"
					:watch-slides-progress="true"
					:prevent-interaction-on-transition="true"
					:breakpoints="{
						0: { slidesPerView: 1, spaceBetween: 10, slidesPerGroup: 1, loopedSlides: 1 },
						600: { slidesPerView: 2, spaceBetween: 12, slidesPerGroup: 1, loopedSlides: 2 },
						768: { slidesPerView: 3, spaceBetween: 15, slidesPerGroup: 1, loopedSlides: 3 },
						1200: { slidesPerView: 4, spaceBetween: 15, slidesPerGroup: 1, loopedSlides: 4 }
					}"
				>
					<SwiperSlide v-for="(review, index) in reviews" :key="`review-${index}`">
						<div class="swiper-card">
							<div class="trustpilot-review">
								<div class="review-header">
									<div class="reviewer-name">{{ review.name }}</div>
									<div class="review-date">{{ review.date }}</div>
								</div>

								<div class="review-title">
									{{ review.title }}
								</div>

								<div class="review-rating">
									<span v-for="i in 5" :key="i" class="star">★</span>
								</div>

								<div class="review-text">
									{{ review.text }}
								</div>

								<div class="review-footer">
									<span class="trustpilot-logo">Trustpilot</span>
								</div>
							</div>
						</div>
					</SwiperSlide>
				</Swiper>

				<div v-else class="text-center w-100 py-5">
					<p>Customer reviews will be available soon.</p>
				</div>
			</div>
			<div class="trustpilot-controls d-flex justify-content-between mt-4 p-2">
				<span class="swiper-nav swiper-prev-btn">
					<i class="fas fa-arrow-left"></i>
				</span>
				<div class="trustpilot-swiper-pagination swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"></div>
				<span class="swiper-nav swiper-next-btn">
					<i class="fas fa-arrow-right"></i>
				</span>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import { getTrustpilotReviews } from '@/api/reviews'

const modules = [Navigation, Pagination, Autoplay]
const reviews = ref([])
const isLoading = ref(true)
const error = ref(null)

const navigation = {
	prevEl: '.swiper-prev-btn',
	nextEl: '.swiper-next-btn'
}

const pagination = {
	el: '.trustpilot-swiper-pagination',
	clickable: true
}

const fetchReviews = async () => {
	try {
		isLoading.value = true
		const response = await getTrustpilotReviews()
		reviews.value = response?.data?.reviews || []
	} catch (err) {
		console.error('Failed to load Trustpilot reviews:', err)
		error.value = err.message
		reviews.value = []
	} finally {
		isLoading.value = false
	}
}

onMounted(() => {
	if (!import.meta.env.SSR) {
		fetchReviews()
	}
})
</script>

<style scoped>
</style>

