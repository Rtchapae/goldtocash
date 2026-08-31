<template>
	<section class="wsa-items" aria-labelledby="wsa-items-title">
		<div class="wsa-items__shell">
			<div class="wsa-items__panel">
				<header class="wsa-items__header">
					<h2 id="wsa-items-title" class="wsa-items__title">Items Gold to Cash will purchase</h2>
					<p class="wsa-items__intro">
						We buy nearly any form of gold, including any scrap gold, gold jewelry and bullion.
						The most common items we work with include:
					</p>
				</header>

				<div class="wsa-items__row">
					<figure class="wsa-items__figure">
						<img
							class="wsa-items__img"
							src="/images/what-sets-apart/items-hero.webp"
							width="694"
							height="463"
							loading="lazy"
							decoding="async"
							alt="Assorted gold jewelry and scrap gold pieces"
						/>
					</figure>

					<ul class="wsa-items__list" role="list">
						<li v-for="(item, i) in items" :key="i" class="wsa-items__li">{{ item }}</li>
					</ul>
				</div>

				<p class="wsa-items__footer">
					If it’s made of gold, you can be confident that you will get an offer from us.
				</p>
			</div>
		</div>
	</section>
</template>

<script setup>
/** Figma 675:2110 — Items Gold to Cash will purchase */
const items = [
	'Gold Chains, Bracelets, Rings',
	'Broken, Damaged or mismatched gold jewelry',
	'Gold Coins, bars, and nuggets',
	'Dental Gold or Gold Fillings',
	'Gold Watches and watch cases',
	'Gold Flakes, Filings and refinery scrap',
	'Gold wire, contacts, and industrial gold',
]
</script>

<style scoped>
/* Figma 675:2110 — white page, cream panel, photo overhang + bulleted list */
.wsa-items {
	width: 100%;
	background: #fff;
	padding: 120px 0 60px;
	box-sizing: border-box;
	overflow-x: clip;
}

.wsa-items__shell {
	max-width: 1530px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.wsa-items__panel {
	background: #fff9ee;
	max-width: 1350px;
	margin: 0 0 0 auto;
	padding: 56px 40px 64px 48px;
	box-sizing: border-box;
	overflow: visible;
}

.wsa-items__header {
	max-width: 1101px;
	margin: 0 0 60px;
	display: flex;
	flex-direction: column;
	gap: 15px;
}

.wsa-items__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #000;
}

.wsa-items__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1.125rem, 2vw, 24px);
	line-height: 32px;
	color: #c39e3d;
}

.wsa-items__row {
	display: grid;
	grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
	gap: clamp(32px, 5vw, 81px);
	align-items: center;
}

.wsa-items__figure {
	margin: 0;
	position: relative;
	z-index: 1;
	width: 100%;
}

.wsa-items__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 694 / 463;
	object-fit: cover;
}

.wsa-items__list {
	list-style: disc;
	margin: 0;
	padding: 0 0 0 1.25rem;
	min-width: 0;
	max-width: 575px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.wsa-items__li {
	margin: 0;
	padding: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1.125rem, 1.8vw, 24px);
	line-height: 32px;
	color: #000;
}

.wsa-items__li::marker {
	color: #000;
}

.wsa-items__footer {
	margin: 60px 0 0;
	max-width: 1117px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #303030;
}

@media (min-width: 992px) {
	/* Photo peeks out of the cream panel (like hsg-items) */
	.wsa-items__figure {
		margin-left: clamp(-140px, -10vw, -80px);
	}
}

@media (max-width: 991.98px) {
	.wsa-items {
		padding: 64px 0 48px;
		background: #fff4d3;
	}

	.wsa-items__shell {
		padding: 0 24px;
	}

	.wsa-items__panel {
		background: transparent;
		padding: 0;
		margin: 0;
	}

	.wsa-items__header {
		margin-bottom: 40px;
		text-align: center;
		align-items: center;
	}

	.wsa-items__title {
		font-size: 30px;
		line-height: 40px;
	}

	.wsa-items__intro {
		color: #b8860b;
		line-height: 1.35;
	}

	.wsa-items__row {
		grid-template-columns: 1fr;
		gap: 40px;
	}

	.wsa-items__figure {
		max-width: 520px;
		margin: 0 auto;
	}

	.wsa-items__list {
		max-width: none;
		gap: 25px;
	}

	.wsa-items__li {
		font-size: 20px;
		line-height: 24px;
	}

	.wsa-items__footer {
		margin-top: 40px;
		text-align: center;
	}
}
</style>
