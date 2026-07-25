<template>
	<section class="wsa-prices" aria-labelledby="wsa-prices-title">
		<div class="wsa-prices__inner">
			<header class="wsa-prices__header">
				<h2 id="wsa-prices-title" class="wsa-prices__title">Gold Standard Prices</h2>
			</header>

			<ul class="wsa-prices__cards" role="list">
				<li v-for="(card, i) in cards" :key="i" class="wsa-prices__card">
					<img
						class="wsa-prices__icon"
						:src="card.iconSrc"
						alt=""
						width="92"
						height="92"
						loading="lazy"
						decoding="async"
					/>
					<div class="wsa-prices__copy">
						<h3 class="wsa-prices__card-title">{{ card.title }}</h3>
						<p class="wsa-prices__card-text">{{ card.text }}</p>
					</div>
				</li>
			</ul>
		</div>
	</section>
</template>

<script setup>
/** Figma 814:1297 — section.calc_pay Gold Standard Prices */
const cards = [
	{
		title: 'Highest Market\nPayouts',
		text: 'no flat rates here! cash offers are calculated from current spot pricing.',
		iconSrc: '/images/what-sets-apart/icon-analysis.png?v=3',
	},
	{
		title: 'Minimum\nOverhead Costs',
		text: 'by processing our transactions online, it allows us to minimize expenses to offer the best prices.',
		iconSrc: '/images/what-sets-apart/icon-coins.png?v=3',
	},
	{
		title: 'Hassle Free',
		text: 'simple buying process, no long negotiations or low-ball offers',
		iconSrc: '/images/what-sets-apart/icon-cash.png?v=3',
	},
]
</script>

<style scoped>
/* Figma 814:1297 — white section, 48px title, bare icon columns (no card chrome) */
.wsa-prices {
	width: 100%;
	background: #fff;
	padding: 80px 0 141px;
	box-sizing: border-box;
}

.wsa-prices__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.wsa-prices__header {
	text-align: center;
	width: 100%;
}

.wsa-prices__title {
	margin: 0;
	padding: 30px 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #000;
	text-align: center;
}

.wsa-prices__cards {
	list-style: none;
	margin: 0;
	padding: 13px 0;
	width: 100%;
	max-width: 1230px;
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 390px));
	justify-content: center;
	gap: 30px;
	box-sizing: border-box;
}

.wsa-prices__card {
	margin: 0;
	min-height: 302px;
	padding: 0;
	background: transparent;
	border: none;
	border-radius: 0;
	box-shadow: none;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	gap: 30px;
	text-align: center;
}

.wsa-prices__icon {
	display: block;
	width: 92px;
	height: 92px;
	object-fit: contain;
	flex-shrink: 0;
}

.wsa-prices__copy {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 10px;
	width: 100%;
	/* Figma Frame 3917 ~306px (card 1 title renders ~251px) */
	max-width: 306px;
}

.wsa-prices__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 30px;
	line-height: 34px;
	color: #000;
	white-space: pre-line;
	text-align: center;
}

.wsa-prices__card-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #000;
	text-align: center;
}

@media (max-width: 991.98px) {
	.wsa-prices {
		padding: 64px 0 80px;
	}

	.wsa-prices__title {
		padding: 12px 0;
		line-height: 1.2;
	}

	.wsa-prices__cards {
		grid-template-columns: 1fr;
		gap: 40px;
		max-width: 390px;
		padding: 0;
	}

	.wsa-prices__card {
		min-height: 0;
	}
}
</style>
