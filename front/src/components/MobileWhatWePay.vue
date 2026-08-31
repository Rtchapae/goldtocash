<template>
	<div class="p-3 mt-3 mobile-whatwepay">
		<div>
			<h1 class="text-center mobile-whatwepay-title">What We Pay</h1>
			<p class="mt-4 mobile-whatwepay-desc">We offer the highest cash for gold jewelry and bullion.</p>
			<div class="d-flex justify-content-between mt-4">
				<span class="font-weight-bold recent-payouts-title">Recent Payouts</span>
				<a href="/what-we-pay" class="view-more-link">
					<img src="/images/eye-gold.svg" class="mr-1" alt="">
					View All
				</a>
			</div>
			<Swiper
				class="mobile-payout-swiper"
				:modules="[Pagination]"
				:slides-per-view="1"
				:space-between="16"
				:centered-slides="true"
				:pagination="{ clickable: true }"
			>
				<SwiperSlide v-for="(p, i) in payouts" :key="i">
					<div class="recent-payout">
						<img :src="p.img" :alt="p.title" class="recent-payout-image">
						<div class="recent-payout-bottom">
							<p>{{ p.title }}</p>
							<p v-html="p.subtitle"></p>
							<p>{{ p.amount }}</p>
						</div>
					</div>
				</SwiperSlide>
			</Swiper>
		</div>
	</div>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/pagination'

const payouts = [
	{ title: 'Gold Necklace', subtitle: '17.5g 14k Gold', img: '/images/payouts/gold-necklace.jpg', amount: '$506.62' },
	{ title: 'Gold Buffalo', subtitle: '31.1g 24k Gold', img: '/images/payouts/gold-buffalo.jpg', amount: '$1,800.00' },
	{ title: 'Gold Ring', subtitle: '10.7g 14k Gold', img: '/images/payouts/gold-ring.jpg', amount: '$290.49' },
	{ title: 'Gold Pendant', subtitle: '7.2g 14k Gold', img: '/images/payouts/gold-pendant.jpg', amount: '$208.00' }
]
</script>

<style scoped>
.mobile-whatwepay {
	background-color: #F5F1E7;
}

.mobile-whatwepay-title {
	margin-top: 0.75rem;
	margin-left: auto;
	margin-right: auto;
	max-width: fit-content;
	color: #000;
	text-align: center;
	font-family: Merriweather, serif;
	font-size: 44px;
	font-style: normal;
	font-weight: 400;
	line-height: 48px;
}

.mobile-whatwepay-desc {
	max-width: 268px;
	margin-left: auto;
	margin-right: auto;
	box-sizing: border-box;
	padding-left: 0;
	padding-right: 0;
	text-align: center;
}

.recent-payouts-title {
	color: #000;
	font-family: Montserrat, sans-serif;
	font-size: 18px;
	font-style: normal;
	font-weight: 600;
	line-height: 22px;
}

.view-more-link {
	color: var(--primary);
	letter-spacing: 0.1em;
}
</style>
