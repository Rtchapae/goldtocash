<template>
	<div class="p-3 mt-5" style="background-color: #F5F1E7;">
		<div>
			<h1 class="text-center mt-5">What We Pay</h1>
			<p class="mt-4 px-4">We offer the highest cash for gold jewelry and bullion.</p>
			<div class="d-flex justify-content-between mt-4">
				<span class="font-weight-bold recent-payouts-title">Recent Payouts</span>
				<a href="/what-we-pay" class="view-more-link">
					<img src="/images/eye-gold.svg" class="mr-1">
					View More
				</a>
			</div>
			<div class="d-flex flex-wrap">
				<div class="swiper recent-payout-swiper swiper-initialized swiper-horizontal swiper-pointer-events">
					<div class="swiper-wrapper recent-payout-swiper-wrapper" style="transition-duration: 0ms;">
						<div class="swiper-slide swiper-slide-duplicate swiper-slide-duplicate-active" data-swiper-slide-index="0">
							<div class="swiper-card">
								<div class="recent-payout">
									<img src="/images/gold-necklace-3.jpg" class="recent-payout-image">
									<div class="recent-payout-bottom">
										<p>Gold Necklace</p>
										<p>17.5G 14K GOLD</p>
										<p>$506.62</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<style scoped>
.recent-payouts-title {
	font-size: 1rem;
}

.view-more-link {
	color: var(--primary);
	letter-spacing: 0.1em;
}
</style>
