<template>
	<section class="wwb-categories" aria-labelledby="wwb-categories-title">
		<div class="wwb-categories__inner">
			<h1 id="wwb-categories-title" class="wwb-categories__title">What We Buy</h1>

			<div class="wwb-categories__panel">
				<div class="wwb-categories__tabs" role="tablist" aria-label="Metal type">
					<button
						type="button"
						role="tab"
						class="wwb-categories__tab"
						:class="{ 'wwb-categories__tab--active': tab === 'gold' }"
						:aria-selected="tab === 'gold'"
						@click="tab = 'gold'"
					>
						GOLD
					</button>
					<button
						type="button"
						role="tab"
						class="wwb-categories__tab"
						:class="{ 'wwb-categories__tab--active': tab === 'silver' }"
						:aria-selected="tab === 'silver'"
						@click="tab = 'silver'"
					>
						SILVER
					</button>
				</div>

				<ul class="wwb-categories__grid" role="list">
					<li
						v-for="item in activeItems"
						:key="tab + '-' + item.slug"
						class="wwb-categories__card"
						:class="{ 'wwb-categories__card--silver': tab === 'silver' }"
					>
						<img
							class="wwb-categories__img"
							:src="item.src"
							:alt="item.label"
							width="220"
							height="220"
							loading="lazy"
							decoding="async"
						/>
						<span class="wwb-categories__overlay">{{ item.label }}</span>
					</li>
				</ul>

				<p class="wwb-categories__note">
					We accept items of all purities and sizes - New, used, broken and damaged.
				</p>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed, ref } from 'vue'

const tab = ref('gold')

const goldItems = [
	{ slug: 'rings', label: 'Rings', src: '/images/what-we-buy/rings.webp' },
	{ slug: 'necklaces', label: 'Necklaces', src: '/images/what-we-buy/necklaces.webp' },
	{ slug: 'earrings', label: 'Earrings', src: '/images/what-we-buy/earrings.webp' },
	{ slug: 'bracelets', label: 'Bracelets', src: '/images/what-we-buy/bracelets.webp' },
	{ slug: 'pendants', label: 'Pendants', src: '/images/what-we-buy/pendants.webp' },
	{ slug: 'dentures', label: 'Dentures', src: '/images/what-we-buy/dentures.webp' },
	{ slug: 'nuggets', label: 'Nuggets', src: '/images/what-we-buy/nuggets.webp' },
	{ slug: 'granules', label: 'Granules', src: '/images/what-we-buy/granules.webp' },
	{ slug: 'designer-jewelry', label: 'Designer Jewelry', src: '/images/what-we-buy/designer-jewelry.webp' },
	{ slug: 'luxury-watches', label: 'Luxury Watches', src: '/images/what-we-buy/luxury-watches.webp' },
	{ slug: 'gold-bars', label: 'Gold Bars', src: '/images/what-we-buy/gold-bars.webp' },
	{ slug: 'class-rings', label: 'Class Rings', src: '/images/what-we-buy/class-rings.webp' },
]

/** Silver tab — Figma labels; reuse stock photos until dedicated silver assets exist. */
const silverItems = [
	{ slug: 's-coins', label: 'Pure Silver Coins', src: '/images/what-we-buy/gold-bars.webp' },
	{ slug: 's-bars', label: 'Silver Bars', src: '/images/what-we-buy/nuggets.webp' },
	{ slug: 's-rounds', label: 'Rounds, Bullion', src: '/images/what-we-buy/granules.webp' },
	{ slug: 's-collectible', label: 'Collectible Coins', src: '/images/what-we-buy/class-rings.webp' },
	{ slug: 's-1964', label: 'Pre-1964 Coins', src: '/images/what-we-buy/pendants.webp' },
	{ slug: 's-jewelry', label: 'Silver Jewelry', src: '/images/what-we-buy/bracelets.webp' },
]

const activeItems = computed(() => (tab.value === 'silver' ? silverItems : goldItems))
</script>

<style scoped>
.wwb-categories {
	width: 100%;
	background: #f5f1e7;
	/* Clear fixed navbar (+ top banner on desktop) */
	padding: 150px 0 60px;
	box-sizing: border-box;
}

.wwb-categories__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.wwb-categories__title {
	margin: 0 0 32px;
	text-align: center;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 1.17;
	color: #000;
}

.wwb-categories__panel {
	background: #fff;
	border: 1px solid #000;
	border-radius: 8px;
	padding: 32px 28px 28px;
	box-sizing: border-box;
	position: relative;
}

.wwb-categories__tabs {
	display: flex;
	justify-content: center;
	gap: 0;
	margin: -32px auto 28px;
	width: fit-content;
}

.wwb-categories__tab {
	appearance: none;
	border: 1px solid #000;
	background: #fff;
	color: #c39e3d;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 16px;
	letter-spacing: 0.04em;
	padding: 12px 36px;
	cursor: pointer;
	border-radius: 8px 8px 0 0;
	margin-bottom: -1px;
}

.wwb-categories__tab--active {
	background: #000;
	color: #fff;
	border-bottom-color: #000;
}

.wwb-categories__grid {
	list-style: none;
	margin: 0;
	padding: 0;
	display: grid;
	grid-template-columns: repeat(4, minmax(0, 1fr));
	gap: 20px;
}

.wwb-categories__card {
	position: relative;
	margin: 0;
	overflow: hidden;
	background: #000;
	border: 1px solid #000;
	border-radius: 4px;
	box-shadow: 8px 8px 0 0 #c39e3d;
}

.wwb-categories__card--silver {
	box-shadow: 8px 8px 0 0 #9a9a9a;
}

.wwb-categories__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 1;
	object-fit: cover;
}

.wwb-categories__overlay {
	position: absolute;
	left: 0;
	right: 0;
	bottom: 0;
	padding: 28px 10px 14px;
	text-align: center;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(0.85rem, 1.5vw, 16px);
	color: #fff;
	text-shadow: 0 1px 3px rgba(0, 0, 0, 0.65);
	background: linear-gradient(transparent, rgba(0, 0, 0, 0.72));
	pointer-events: none;
}

.wwb-categories__note {
	margin: 28px 0 0;
	text-align: center;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 16px;
	line-height: 1.45;
	color: #000;
}

@media (max-width: 991.98px) {
	.wwb-categories__grid {
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 14px;
	}

	.wwb-categories {
		padding: 140px 0 40px;
	}

	.wwb-categories__panel {
		padding: 28px 16px 20px;
	}
}
</style>
