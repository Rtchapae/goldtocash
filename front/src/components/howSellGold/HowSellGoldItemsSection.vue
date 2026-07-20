<template>
	<section class="hsg-items" aria-labelledby="hsg-items-title">
		<div class="hsg-items__shell">
			<div class="hsg-items__panel">
				<header class="hsg-items__header">
					<h2 id="hsg-items-title" class="hsg-items__title">
						Turn These Gold or Silver Items into Cash
					</h2>
					<p class="hsg-items__intro">
						If you have any of the following items, we will happily offer you a cash payout!
					</p>
				</header>

				<div class="hsg-items__row">
					<figure class="hsg-items__figure">
						<img
							class="hsg-items__img"
							src="/images/how-sell-gold/items-hero.webp?v=white"
							width="694"
							height="463"
							loading="lazy"
							decoding="async"
							alt="Mixed gold and silver jewelry ready to sell"
						/>
					</figure>

					<div class="hsg-items__lists">
						<div class="hsg-items__group">
							<h3 class="hsg-items__group-title">GOLD</h3>
							<ul class="hsg-items__ul" role="list">
								<li v-for="(item, i) in goldItems" :key="'g-' + i">{{ item }}</li>
							</ul>
						</div>
						<div class="hsg-items__group">
							<h3 class="hsg-items__group-title">SILVER</h3>
							<ul class="hsg-items__ul" role="list">
								<li v-for="(item, i) in silverItems" :key="'s-' + i">{{ item }}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
const goldItems = [
	'Ring, Class Rings, Necklaces, Earrings, Bracelets, Pendants,',
	'Dentures',
	'Nuggets, Granules',
	'Gold Designer',
	'Jewelry, Gold Luxury Watches',
	'Bullion / Bars',
	'Coins',
]

const silverItems = [
	'Pure Silver Coins',
	'Silver Bars',
	'Rounds, Bullion',
	'Collective Coins - Dimes',
	'Dimes, Quarters and half dollars dated 1964 or older',
]
</script>

<style scoped>
/* Mobile-first (Figma 641:1272): full-bleed beige. Desktop adds white frame + photo overhang. */
.hsg-items {
	width: 100%;
	background: #fff4d3;
	padding: 64px 0;
	box-sizing: border-box;
	overflow-x: clip;
}

.hsg-items__shell {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.hsg-items__panel {
	background: transparent;
	width: 100%;
	max-width: none;
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	overflow: visible;
}

.hsg-items__header {
	width: 100%;
	max-width: none;
	margin: 0 0 28px;
	text-align: center;
}

.hsg-items__title {
	display: block;
	width: 100%;
	max-width: none;
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.5vw, 40px);
	line-height: 1.2;
	color: #000;
}

.hsg-items__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 18px;
	line-height: 1.45;
	color: #303030;
}

.hsg-items__row {
	display: grid;
	grid-template-columns: 1fr;
	gap: 28px;
	align-items: center;
}

.hsg-items__figure {
	margin: 0;
	width: 100%;
	position: relative;
	z-index: 1;
	background: #fff;
	box-shadow: 10px 10px 0 0 #c39e3d;
}

.hsg-items__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 694 / 463;
	object-fit: cover;
	background: #fff;
}

.hsg-items__lists {
	display: flex;
	flex-direction: column;
	gap: 28px;
	min-width: 0;
}

.hsg-items__group-title {
	margin: 0 0 12px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 26px;
	letter-spacing: 0.04em;
	color: #c39e3d;
	text-align: center;
}

.hsg-items__ul {
	margin: 0 auto;
	padding: 0 0 0 1.15rem;
	max-width: 320px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 16px;
	line-height: 1.55;
	color: #000;
	text-align: left;
}

.hsg-items__ul li + li {
	margin-top: 6px;
}

@media (min-width: 992px) {
	.hsg-items {
		background: #fff;
		padding: 100px 0;
	}

	.hsg-items__panel {
		background: #fff4d3;
		padding: 56px 40px 64px 48px;
	}

	.hsg-items__header {
		margin: 0 0 40px;
		text-align: left;
	}

	.hsg-items__row {
		grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
		gap: 40px;
	}

	/* Photo peeks out of the beige panel (Figma ~166px) */
	.hsg-items__figure {
		margin-left: clamp(-140px, -10vw, -80px);
		width: auto;
	}

	.hsg-items__group-title {
		text-align: left;
	}

	.hsg-items__ul {
		margin: 0;
		max-width: none;
	}
}
</style>
