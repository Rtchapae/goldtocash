<template>
	<section class="wwb-trust" aria-labelledby="wwb-trust-title">
		<div class="wwb-trust__shell">
			<div class="wwb-trust__panel">
				<header class="wwb-trust__header">
					<h2 id="wwb-trust-title" class="wwb-trust__title">You Can Trust Gold to Cash Because…</h2>
					<p class="wwb-trust__intro">
						We’ve built our reputation on reliable service and competitive payouts, helping thousands of customers
						across the country turn their unwanted gold into real cash quickly and confidently.
					</p>
				</header>

				<div class="wwb-trust__row">
					<figure class="wwb-trust__figure">
						<img
							class="wwb-trust__img"
							src="/images/what-we-buy/trust-hero.webp?v=fill2"
							width="694"
							height="463"
							loading="lazy"
							decoding="async"
							alt="Cash and gold jewelry from a Gold to Cash payout"
						/>
					</figure>

					<ul class="wwb-trust__list" role="list">
						<li v-for="(item, i) in items" :key="i" class="wwb-trust__item">
							<h3 class="wwb-trust__item-title">{{ item.title }}</h3>
							<p class="wwb-trust__item-text">{{ item.body }}</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
const items = [
	{
		title: 'Our Competitive Pricing:',
		body:
			'Our offers are calculated using up-to-date gold market prices, so your payout reflects the true value of your gold. Unlike some buyers who rely on fixed rates, our pricing adjusts with the current market.',
	},
	{
		title: 'No Hassle promise:',
		body:
			'Our goal is to keep the process simple and straightforward. There’s no pressure to sell, no low-ball offers, and no drawn-out negotiations - just a clear and honest offer.',
	},
	{
		title: 'Low Overhead = Higher Payouts:',
		body:
			'Because our process is primarily online, we keep operating costs low. That allows us to pass more value back to our customers through stronger payouts.',
	},
	{
		title: 'Reliable Evaluations:',
		body:
			'Every item we receive is carefully weighed and tested for purity by trained specialists using professional equipment to ensure accurate results.',
	},
	{
		title: 'Fast Offers:',
		body:
			'Once your gold arrives at our facility, you can expect to receive a quote from our team within 24 hours.',
	},
	{
		title: 'Free & Fully Insured Shipping:',
		body:
			'Our mail-in kits are safe, easy to use, and completely free. Your shipment is insured for protection, making the process both risk-free and convenient from start to finish.',
	},
]
</script>

<style scoped>
/*
 * Figma 675:1998 / mobile 681:2871 — same pattern as how-sell .hsg-items:
 * mobile full-bleed cream; desktop white frame + cream panel + photo overhang.
 */
.wwb-trust {
	width: 100%;
	background: #fff9ee;
	padding: 64px 0;
	box-sizing: border-box;
	overflow: visible;
}

.wwb-trust__shell {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.wwb-trust__panel {
	background: transparent;
	width: 100%;
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	overflow: visible;
}

.wwb-trust__header {
	width: 100%;
	margin: 0 0 28px;
	text-align: center;
}

.wwb-trust__title {
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.2vw, 48px);
	line-height: 1.2;
	color: #000;
}

.wwb-trust__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.45;
	color: #b8860b;
}

.wwb-trust__row {
	display: grid;
	grid-template-columns: 1fr;
	gap: 28px;
	align-items: start;
}

.wwb-trust__figure {
	margin: 0;
	width: 100%;
	position: relative;
	z-index: 1;
	background: #fff;
	box-shadow: 10px 10px 0 0 #c39e3d;
}

.wwb-trust__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 694 / 463;
	object-fit: cover;
	object-position: left center;
	background: #fff;
}

.wwb-trust__list {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 22px;
}

.wwb-trust__item-title {
	margin: 0 0 6px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 18px;
	line-height: 1.35;
	color: #000;
}

.wwb-trust__item-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: 16px;
	line-height: 1.5;
	color: #000;
}

@media (min-width: 992px) {
	.wwb-trust {
		background: #fff;
		padding: 100px 0;
	}

	.wwb-trust__panel {
		background: #fff9ee;
		padding: 56px 40px 64px 48px;
	}

	.wwb-trust__header {
		margin: 0 0 40px;
		text-align: left;
	}

	.wwb-trust__row {
		grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
		gap: 40px;
		align-items: center;
	}

	/* Photo peeks out of the cream panel onto white (Figma ~166px) */
	.wwb-trust__figure {
		margin-left: clamp(-166px, -12vw, -100px);
		width: auto;
	}

	.wwb-trust__item-title {
		font-size: 20px;
	}

	.wwb-trust__item-text {
		font-size: 18px;
	}
}
</style>
