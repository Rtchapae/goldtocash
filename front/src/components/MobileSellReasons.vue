<template>
	<div class="text-center">
		<!-- Figma 2679-1927: Penny Hoarder badge + text -->
		<div class="penny-badge-block">
			<img src="/images/penny-hoarder-badge.png" alt="Best Gold Buyer" class="penny-badge-img" width="103" height="84" />
			<p class="penny-badge-text">Rated "Best Gold Buyer" by Penny Hoarder and Think Save Retire.</p>
		</div>

		<!-- Figma 2680-1981: Reviews block (same data as TrustpilotDesktop, mobile style + carousel) -->
		<MobileTrustpilotReviews />

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
import GoldCalculator from '@/components/GoldCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'
import MobileHowItWorksBlock from '@/components/MobileHowItWorksBlock.vue'
import MobileTrustpilotReviews from '@/components/MobileTrustpilotReviews.vue'

function scrollToTop() {
	window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>
