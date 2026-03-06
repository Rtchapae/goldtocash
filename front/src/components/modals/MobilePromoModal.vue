<template>
	<div class="promo-modal-root">
		<div class="promo-modal-overlay" @click="$emit('close')"></div>
		<div id="popup" class="active promo-panel">
			<img class="promo-preload" loading="eager" src="/images/woman-holding-cash-1.png">
			<div class="row">
				<div class="col-12 d-md-none">
					<div id="mobile-pane">
						<img src="/images/white.svg" class="logo promo-logo">
						<span class="close-btn" @click="$emit('close')">
							<i class="fa fa-times"></i>
						</span>
						<h1 class="promo-title">10%</h1>
						<h3>
							Get an extra 10%
							<br>
							for your gold.
						</h3>
						<button class="btn btn-kit btn-green form-btn promo-cta-btn" data-toggle="modal"
							data-target="#getStartedModal">
							Request Free Appraisal Kit
							<br>
							<span class="promo-bonus">+ 10% Bonus</span>
						</button>
						<p class="promo-shipping-text">
							<img src="/images/truck-white.png" class="promo-shipping-icon">
							Free &amp; Insured Shipping
							<b>up to $100,000*</b>
						</p>
					</div>
				</div>
				<div class="d-none d-md-block col-md-5">
					<div id="side-1"></div>
				</div>
				<div class="d-none d-md-block col-md-7 text-center">
					<div id="side-2">
						<span class="close-btn" @click="$emit('close')">
							<i class="fa fa-times"></i>
						</span>
						<h1 class="promo-title">10%</h1>
						<h3>
							Get an extra 10%
							<br>
							for your gold.
						</h3>
						<button class="btn btn-kit btn-green form-btn promo-cta-btn" data-toggle="modal"
							data-target="#getStartedModal">
							Request Free Appraisal Kit
							<br>
							<span class="promo-bonus">+ 10% Bonus</span>
						</button>
						<p class="promo-shipping-text">
							<img src="/images/truck-white.png" class="promo-shipping-icon">
							Free &amp; Insured Shipping
							<b>up to $100,000*</b>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
</script>

<style scoped>
.promo-modal-root { position: fixed; inset: 0; z-index: 10000; }
.promo-modal-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.6); }
#popup { position: relative; z-index: 1; }
.promo-preload { display: none; }
.promo-title { font-size: 8em; font-weight: bolder; color: var(--primary); }
.promo-cta-btn { border-radius: 100px; font-size: .95em; padding: 8px; }
.promo-bonus { font-size: .8em; color: rgba(255, 255, 255, 0.7); }
.promo-shipping-text { font-size: .8em; margin-top: 2em; }
.promo-shipping-icon { filter: invert(1); width: 1em; margin-right: .4em; margin-top: -4px; }
.promo-logo { width: 240px; }

/* Slide-up panel from bottom */
.promo-panel { position: absolute; left: 0; right: 0; bottom: 0; transform: translateY(100%); animation: promo-slide-up 400ms ease-out forwards; }
@keyframes promo-slide-up { from { transform: translateY(100%); } to { transform: translateY(0); } }
</style>
