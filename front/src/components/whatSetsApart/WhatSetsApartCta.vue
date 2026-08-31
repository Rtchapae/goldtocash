<template>
	<section class="wsa-cta" aria-labelledby="wsa-cta-title">
		<div class="wsa-cta__inner">
			<figure class="wsa-cta__figure">
				<img
					class="wsa-cta__img"
					src="/images/what-sets-apart/cta-hero.webp"
					width="647"
					height="440"
					loading="lazy"
					decoding="async"
					alt="Unused gold jewelry ready to turn into cash"
				/>
			</figure>
			<div class="wsa-cta__copy">
				<h2 id="wsa-cta-title" class="wsa-cta__title">Turn Unused Gold into Cash Today!</h2>
				<p class="wsa-cta__p">
					Got broken jewelry, old gold you never wear, or pieces tucked away in a drawer? That gold could still be
					worth more than you think. Many people are surprised to learn that even damaged or outdated jewelry still
					has real value because of the gold it contains. With gold prices staying strong in today’s market, it may
					be a great time to turn those unused items into extra cash.
				</p>
				<p class="wsa-cta__p">
					Getting started is quick and easy. First, use our Scrap Gold Calculator to get a fast estimate of what your
					gold might be worth based on current market prices. Then simply fill out the short form to request your free
					mail-in appraisal kit. Once we receive your items, our team will carefully test and evaluate your gold using
					professional equipment to determine its purity and value. From there, we’ll send you a competitive cash offer
					along with fast payment options.
				</p>
				<p class="wsa-cta__p">
					The entire process is designed to be simple, secure, and completely risk-free from start to finish.
				</p>
			</div>
		</div>
	</section>
</template>

<style scoped>
.wsa-cta {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.wsa-cta__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	gap: 48px;
	box-sizing: border-box;
}

.wsa-cta__copy {
	flex: 1 1 560px;
	min-width: 0;
}

.wsa-cta__title {
	margin: 0 0 20px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.2vw, 36px);
	line-height: 1.2;
	color: #000;
}

.wsa-cta__p {
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 16px;
	line-height: 1.5;
	color: #000;
}

.wsa-cta__figure {
	flex: 0 0 auto;
	width: min(604px, 48%);
	max-width: 604px;
	margin: 0;
	box-shadow:
		10px 10px 0 0 #c39e3d,
		10px 10px 4px 0 rgba(0, 0, 0, 0.25);
}

.wsa-cta__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 647 / 440;
	object-fit: cover;
}

@media (max-width: 991.98px) {
	.wsa-cta {
		padding: 64px 0;
	}

	.wsa-cta__inner {
		flex-direction: column;
		align-items: stretch;
	}

	.wsa-cta__figure {
		width: 100%;
		max-width: none;
		order: -1;
	}
}
</style>
