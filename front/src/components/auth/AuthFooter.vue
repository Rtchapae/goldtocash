<template>
	<div>
		<div class="container">
			<div class="row w-100">
				<div class="col mb-4">
					<img src="/images/pcgs-dark.svg" class="partner-logo" alt="Icon" loading="lazy">
				</div>
				<div class="col mb-4">
					<img src="/images/jbt-dark.svg" class="partner-logo" alt="Icon" loading="lazy">
				</div>
				<div class="col mb-2">
					<img src="/images/paypal-dark.svg" class="partner-logo" alt="Icon" loading="lazy">
				</div>
				<div class="col mb-2">
					<img src="/images/fedex-dark.svg" class="partner-logo" alt="Icon" loading="lazy">
				</div>
				<div class="col mb-2">
					<img src="/images/ngc-dark.svg" class="partner-logo" alt="Icon" loading="lazy">
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row w-100">
				<div class="col-12 text-center">
					<p class="my-2 consent-text">
						* Please refer to <a href="/terms-and-conditions">Terms and Conditions</a>
						to assure and eliminate, as best as possible, any confusion you may have about our
						business practices.
					</p>
					<p class="my-2 consent-text">
						Licensed and operating under Washington State Chapter 19.60 RCW Secondhand Precious
						Metals Dealer.
					</p>
					<p class="my-3 consent-text">
						Copyright &copy; 2020-{{ currentYear }} <a href="https://www.goldtocash.us">Gold to Cash</a> All
						Rights Reserved
					</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
const currentYear = new Date().getFullYear()
</script>

<style scoped>
.partner-logo {
	opacity: 0.2;
}

.consent-text,
.consent-text a {
	font-size: 12px;
	line-height: 16px;
	color: #cccccc;
	text-align: center;
	margin: 0;
}
</style>

