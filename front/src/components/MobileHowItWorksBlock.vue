<template>
	<!-- Figma 2680-2086: How it works block after calculator -->
	<div class="how-it-works-block">
		<h2 class="how-it-works-block-title">How it works</h2>
		<div class="how-it-works-block-card">
			<div class="how-it-works-steps">
				<div class="how-it-works-step">
					<div class="how-it-works-step-badge"><span>1</span></div>
					<h3 class="how-it-works-step-title">REQUEST YOUR FREE<br>Appraisal Kit</h3>
					<p class="how-it-works-step-desc">No risk. No obligation.<br>Give us a try!</p>
				</div>
				<div class="how-it-works-step">
					<div class="how-it-works-step-badge"><span>2</span></div>
					<h3 class="how-it-works-step-title">SHIP YOUR VALUABLES</h3>
					<p class="how-it-works-step-desc">Free &amp; insured FedEx shipping<br>up to $100,000*.</p>
				</div>
				<div class="how-it-works-step">
					<div class="how-it-works-step-badge"><span>3</span></div>
					<h3 class="how-it-works-step-title">Accept Our Offer &amp; Get Paid</h3>
					<p class="how-it-works-step-desc">Highest payout guaranteed,<br>in 24 hours.</p>
				</div>
			</div>
			<button type="button" class="btn how-it-works-block-cta" @click="scrollToTop">
				Get My free Kit
			</button>
		</div>
	</div>
</template>

<script setup>
function scrollToTop() {
	window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style scoped>
.how-it-works-block {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 24px;
	padding: 48px 16px;
	border-bottom: 1px solid #E0E0E0;
}
.how-it-works-block-title {
	color: #000;
	text-align: center;
	font-family: Merriweather, serif;
	font-size: 44px;
	font-weight: 400;
	line-height: 1.09em;
	margin: 0;
}
.how-it-works-block-card {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 24px;
	padding: 24px;
	background: #E7F6EB;
	border-radius: 12px;
}
.how-it-works-steps {
	display: flex;
	flex-direction: column;
	gap: 16px;
}
.how-it-works-step {
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;
	gap: 4px;
}
.how-it-works-step-badge {
	display: flex;
	align-items: center;
	justify-content: center;
	width: 24px;
	height: 24px;
	border-radius: 100px;
	background: #11BA69;
	border: 1.5px solid #11BA69;
	color: #fff;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 12px;
	line-height: 2em;
	letter-spacing: 0.08em;
	text-transform: uppercase;
}
.how-it-works-step-title {
	margin: 0;
	color: #000;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 14px;
	line-height: 1.43em;
	letter-spacing: 0.08em;
	text-transform: uppercase;
}
.how-it-works-step-desc {
	margin: 0;
	color: rgba(0, 0, 0, 0.5);
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: 14px;
	line-height: 1.43em;
}
.how-it-works-block-cta {
	width: 100%;
	padding: 16px 0;
	background: #11BA69;
	color: #fff;
	border: none;
	border-radius: 4px;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 16px;
	line-height: 1.5em;
	cursor: pointer;
}
.how-it-works-block-cta:hover {
	background: #0fa55c;
	color: #fff;
}
</style>
