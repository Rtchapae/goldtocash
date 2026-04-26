<template>
	<div class="text-center">
		<!-- Figma 2679-1927: Penny Hoarder badge + text -->
		<div class="penny-badge-block">
			<img src="/images/penny-hoarder-badge.png" alt="Best Gold Buyer" class="penny-badge-img" width="103" height="84" />
			<p class="penny-badge-text">Rated "Best Gold Buyer" by Penny Hoarder and Think Save Retire.</p>
		</div>

		<!-- Figma 2680-1981: Reviews block (same data as TrustpilotDesktop, mobile style + carousel) -->
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

		<div class="mobile-calculator-section">
			<ClientOnly>
				<GoldCalculator :show-heading="false" />
			</ClientOnly>
		</div>

		<MobileHowItWorksBlock />

		<h2 class="sell-reasons-heading">Sell your gold to a trusted gold buyer</h2>

		<div class="sell-reason-item">
			<div class="d-flex justify-content-between">
				<img class="item-icon" src="/images/trophy.png">
				<div>
					<p class="item-title">A+ Rating</p>
					<p class="item-detail">
						We are proud to present our A+ rating on BBB and 5 star average rating.
					</p>
				</div>
			</div>
		</div>
		<div class="sell-reason-item">
			<div class="d-flex justify-content-between">
				<img class="item-icon" src="/images/box-seam.png">
				<div>
					<p class="item-title">Safe and Fast</p>
					<p class="item-detail">
						Free &amp; Insured FedEx shipping with up to $100,000 insurance*.
					</p>
				</div>
			</div>
		</div>
		<div class="sell-reason-item">
			<div class="d-flex justify-content-between">
				<img class="item-icon" src="/images/thumb-up.png">
				<div>
					<p class="item-title">Satisfaction Guaranteed</p>
					<p class="item-detail">You will get your items back SAFE &amp; FREE if not
						satisfied.</p>
				</div>
			</div>
		</div>
		<div class="sell-reason-item">
			<div class="d-flex justify-content-between">
				<img class="item-icon" src="/images/tag.svg">
				<div>
					<p class="item-title">Price Match Guarantee</p>
					<p class="item-detail">We match competitor's prices.</p>
				</div>
			</div>
		</div>
		<div class="sell-reason-item">
			<div class="d-flex justify-content-between">
				<img class="item-icon" src="/images/scroll.png">
				<div>
					<p class="item-title">Licensed Business</p>
					<p class="field">Licensed and operating under Washington State Chapter 19.60 RCW
						Secondhand
						Precious Metals Dealer.
					</p>
				</div>
			</div>
		</div>
	</div>
	<button type="button" class="btn btn-green btn-kit w-100 mt-3" @click="scrollToTop">
		Get My Free Kit
	</button>
</template>

<script setup>
import { computed, ref } from 'vue'

function scrollToTop() {
	window.scrollTo({ top: 0, behavior: 'smooth' })
}
import { Swiper, SwiperSlide } from 'swiper/vue'
import GoldCalculator from '@/components/GoldCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'
import MobileHowItWorksBlock from '@/components/MobileHowItWorksBlock.vue'
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
