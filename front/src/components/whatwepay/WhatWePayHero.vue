<template>
	<section class="wwp-hero" aria-labelledby="wwp-hero-title">
		<div class="wwp-hero__inner">
			<div class="wwp-hero__copy">
				<h1 id="wwp-hero-title" class="wwp-hero__title">Guaranteed Best Pricing</h1>
				<p class="wwp-hero__eyebrow">How much is my gold | silver worth?</p>
				<p class="wwp-hero__lead">
					At Gold to Cash, we buy gold and silver jewelry in all conditions and purities — broken, worn, mismatched,
					or brand new. Your payout is based on three transparent factors so you always know how we arrive at your
					offer:
				</p>
				<ul class="wwp-hero__list" role="list">
					<li>Current Market Pricing</li>
					<li>Weight</li>
					<li>Purity</li>
				</ul>
			</div>

			<figure class="wwp-hero__figure">
				<img
					class="wwp-hero__img"
					src="/images/what-sets-apart/cta-hero.webp"
					width="647"
					height="440"
					loading="eager"
					decoding="async"
					alt="Unused gold jewelry ready to turn into cash"
				/>
			</figure>
		</div>
	</section>
</template>

<script setup>
/** Figma 1028:1840 / 1221:1876 — What We Pay hero + cta-hero image */
</script>

<style scoped>
.wwp-hero {
	width: 100%;
	background: #fff;
	padding: 160px 0 80px;
	box-sizing: border-box;
}

.wwp-hero__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(0, 0.95fr);
	gap: 48px;
	align-items: center;
}

.wwp-hero__copy {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 20px;
	min-width: 0;
}

.wwp-hero__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4.5vw, 48px);
	line-height: 1.15;
	color: #000;
}

.wwp-hero__eyebrow {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(0.875rem, 1.5vw, 16px);
	line-height: 1.3;
	letter-spacing: 0.04em;
	text-transform: uppercase;
	color: #000;
}

.wwp-hero__lead {
	margin: 0;
	max-width: 560px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.6vw, 18px);
	line-height: 1.5;
	color: #000;
}

.wwp-hero__list {
	margin: 8px 0 0;
	padding: 0;
	list-style: none;
	width: fit-content;
	max-width: 280px;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 8px;
	opacity: 1;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1rem, 1.5vw, 18px);
	line-height: 1.3;
	color: #000;
	box-sizing: border-box;
}

.wwp-hero__list li {
	margin: 0;
	padding-left: 1.1em;
	position: relative;
	width: 100%;
}

.wwp-hero__list li::before {
	content: '•';
	position: absolute;
	left: 0;
	top: 0;
}

.wwp-hero__figure {
	margin: 0;
	width: 100%;
	max-width: 647px;
	justify-self: end;
	box-shadow:
		10px 10px 0 0 #c39e3d,
		10px 10px 4px 0 rgba(0, 0, 0, 0.25);
}

.wwp-hero__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 647 / 440;
	object-fit: cover;
}

@media (max-width: 991.98px) {
	.wwp-hero {
		padding: 120px 0 48px;
	}

	.wwp-hero__inner {
		grid-template-columns: 1fr;
		gap: 32px;
	}

	.wwp-hero__copy {
		align-items: center;
		text-align: center;
	}

	.wwp-hero__list {
		align-self: center;
		text-align: left;
	}

	.wwp-hero__figure {
		justify-self: center;
		order: -1;
		max-width: 520px;
	}
}
</style>
