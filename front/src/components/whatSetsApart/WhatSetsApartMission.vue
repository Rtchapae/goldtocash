<template>
	<section class="wsa-mission" aria-label="About Gold to Cash">
		<div class="wsa-mission__inner">
			<div class="wsa-mission__copy">
				<p class="wsa-mission__p">
					Gold to Cash™ is one of the most reputable and trusted gold buyers in the industry. We are based out of
					beautiful PNW and are licensed under Washington State Chapter 19.60 RCW Secondhand Precious Metals Dealer.
					We are proud to bring great service so you will be confident working with us, be a happy returning customer
					and recommend us to your friends and family. Our offers are hard to beat and our appraisal process is
					amongst the best in the industry; applying the best of two worlds - Experience and Technology! With decades
					of experience in the jewelry and precious metals business, you can be self-possessed to work with us.
				</p>
				<p class="wsa-mission__p">
					We started buying gold jewelry and precious metals bullion over fifteen years ago and we know the industry.
					Many people are treated unfairly and are being paid a fraction of the real worth for their jewelry at pawn
					shops and we want to change that. The best reward for our great service is happy and satisfied customers -
					that is what we strive to achieve!
				</p>
				<p class="wsa-mission__p">
					At Gold to Cash, our mission is to provide fast, safe, secure, and pleasant experience to sell gold jewelry
					and precious metals bullion. On top of that we offer the highest payouts for your valuables and can match
					competitor's prices. When you sell at Gold to Cash you get a transparent report about our offer. You will
					know exactly why you're getting paid this much so you can accept our offer with confidence.
				</p>

				<blockquote class="wsa-mission__quote">
					<p class="wsa-mission__quote-text">
						“We build our reputation on customer service, trust and integrity. These three pillars make up the
						foundation of what we do each and every day.”
					</p>
					<footer class="wsa-mission__quote-footer">
						<p class="wsa-mission__quote-name">Constantine Igumnov</p>
						<p class="wsa-mission__quote-role">Founder - Gold to Cash™</p>
					</footer>
				</blockquote>
			</div>

			<figure class="wsa-mission__figure">
				<img
					class="wsa-mission__img"
					src="/images/what-sets-apart/mission-photo.webp"
					width="604"
					height="440"
					loading="lazy"
					decoding="async"
					alt="Customer and Gold to Cash specialist in our office"
				/>
			</figure>
		</div>
	</section>
</template>

<script setup>
/** Figma 831:1595 — mission / founder story */
</script>

<style scoped>
.wsa-mission {
	width: 100%;
	background: #000;
	padding: 40px 0;
	box-sizing: border-box;
}

.wsa-mission__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: grid;
	grid-template-columns: minmax(0, 605fr) minmax(0, 604fr);
	gap: 47px;
	align-items: start;
}

.wsa-mission__copy {
	display: flex;
	flex-direction: column;
	gap: 20px;
	min-width: 0;
}

.wsa-mission__p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #fff;
}

.wsa-mission__quote {
	margin: 10px 0 0;
	padding: 0;
	border: 0;
}

.wsa-mission__quote-text {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 20px;
	line-height: 26px;
	color: #fff;
}

.wsa-mission__quote-footer {
	margin: 0;
}

.wsa-mission__quote-name,
.wsa-mission__quote-role {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #fff;
}

.wsa-mission__figure {
	margin: 48px 0 0;
	position: relative;
	width: 100%;
	max-width: 604px;
	justify-self: end;
}

.wsa-mission__figure::after {
	content: '';
	position: absolute;
	right: -10px;
	bottom: -10px;
	width: 70%;
	height: 70%;
	border-right: 10px solid #c39e3d;
	border-bottom: 10px solid #c39e3d;
	pointer-events: none;
	z-index: 0;
}

.wsa-mission__img {
	display: block;
	position: relative;
	z-index: 1;
	width: 100%;
	height: auto;
	aspect-ratio: 604 / 440;
	object-fit: cover;
}

@media (max-width: 991.98px) {
	.wsa-mission {
		padding: 48px 0;
	}

	.wsa-mission__inner {
		grid-template-columns: 1fr;
		gap: 32px;
	}

	.wsa-mission__figure {
		order: -1;
		justify-self: center;
		max-width: 520px;
		margin-top: 0;
	}

	.wsa-mission__p,
	.wsa-mission__quote-text,
	.wsa-mission__quote-name,
	.wsa-mission__quote-role {
		font-size: 16px;
		line-height: 1.45;
	}
}
</style>
