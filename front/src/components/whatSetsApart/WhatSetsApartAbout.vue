<template>
	<section class="wsa-about" aria-labelledby="wsa-about-title">
		<div class="wsa-about__inner">
			<header class="wsa-about__header">
				<h2 id="wsa-about-title" class="wsa-about__title">About Us</h2>
				<span class="wsa-about__divider" aria-hidden="true"></span>
			</header>

			<div class="wsa-about__copy">
				<p class="wsa-about__p">
					Gold to Cash™ is a trusted gold jewelry and precious metals bullion buyer based out of beautiful PNW.
					Licensed under Washington State Chapter 19.60 RCW Secondhand Precious Metals Dealer.
				</p>
				<p class="wsa-about__p">
					We are proud to bring great service so you will be confident to work with us, be a happy returning customer
					and recommend us to your friends or family. Our offers are hard to beat and our appraisal process is amongst
					the best in the industry; applying the best of two worlds- Technology and Experience!
				</p>
			</div>
		</div>
	</section>
</template>

<script setup>
/** Figma 660:4338 — About Us with photo background */
</script>

<style scoped>
.wsa-about {
	width: 100%;
	min-height: 734px;
	background:
		linear-gradient(90deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.25) 55%, rgba(0, 0, 0, 0.1) 100%),
		url('/images/what-sets-apart/about-bg.webp') center / cover no-repeat;
	padding: 150px 0;
	box-sizing: border-box;
	display: flex;
	align-items: center;
}

.wsa-about__inner {
	max-width: 1228px;
	width: 100%;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 98px;
}

.wsa-about__header {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 16px;
}

.wsa-about__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #fff;
}

.wsa-about__divider {
	display: block;
	width: 48px;
	height: 2px;
	background: #dbaf3e;
}

.wsa-about__copy {
	max-width: 545px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.wsa-about__p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #fff;
}

@media (max-width: 991.98px) {
	.wsa-about {
		min-height: 0;
		padding: 80px 0;
		background:
			linear-gradient(180deg, rgba(0, 0, 0, 0.72) 0%, rgba(0, 0, 0, 0.5) 100%),
			url('/images/what-sets-apart/about-bg.webp') center / cover no-repeat;
	}

	.wsa-about__inner {
		gap: 40px;
	}

	.wsa-about__title {
		line-height: 1.2;
	}

	.wsa-about__p {
		font-size: 16px;
		line-height: 1.45;
	}
}
</style>
