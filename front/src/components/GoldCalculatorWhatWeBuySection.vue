<template>
	<section
		class="gc-figma-391-912"
		data-figma-node="391-912"
		aria-labelledby="gc-figma-391-912-heading"
	>
		<div class="gc-figma-391-912__shell">
			<div class="gc-figma-391-912__panel">
				<header class="gc-figma-391-912__header">
					<h2 id="gc-figma-391-912-heading" class="gc-figma-391-912__title">{{ sectionTitle }}</h2>
					<p class="gc-figma-391-912__intro">{{ sectionIntro }}</p>
				</header>

				<div class="gc-figma-391-912__row">
					<div class="gc-figma-391-912__figure">
						<img
							class="gc-figma-391-912__img"
							:src="heroImage.src"
							width="500"
							height="596"
							loading="lazy"
							decoding="async"
							:alt="heroImage.alt"
						/>
					</div>

					<ul class="gc-figma-391-912__grid" role="list">
						<li v-for="(item, i) in items" :key="i" class="gc-figma-391-912__cell">
							<span class="gc-figma-391-912__icon" aria-hidden="true">
								<img
									class="gc-figma-391-912__icon-img"
									:src="item.iconSrc"
									alt=""
									width="40"
									height="40"
									loading="lazy"
									decoding="async"
								/>
							</span>
							<span class="gc-figma-391-912__label">{{ item.label }}</span>
						</li>
					</ul>
				</div>

				<p v-if="footerNote" class="gc-figma-391-912__footer">{{ footerNote }}</p>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	pageVariant: {
		type: String,
		default: 'scrap',
		validator: (v) => ['scrap', 'dental', 'calculator'].includes(v),
	},
})

const SCRAP_ITEMS = [
	{ label: 'Broken or mismatched jewelry', iconSrc: '/images/calc/what-we-buy/wwb-jewelry.svg' },
	{ label: 'Gold chains, bracelets, and rings', iconSrc: '/images/calc/what-we-buy/wwb-rings.svg' },
	{ label: 'Gold coins, bars, and nuggets', iconSrc: '/images/calc/what-we-buy/wwb-coins.svg' },
	{ label: 'Dental gold and gold fillings', iconSrc: '/images/calc/what-we-buy/wwb-tooth.svg' },
	{ label: 'Gold watches and watch cases', iconSrc: '/images/calc/what-we-buy/wwb-watch.svg' },
	{ label: 'Gold flakes, filings, and refinery scrap', iconSrc: '/images/calc/what-we-buy/wwb-chains.svg' },
	{ label: 'Gold wire, contacts, or industrial gold', iconSrc: '/images/calc/what-we-buy/wwb-golds.svg' },
]

const DENTAL_ITEMS = [
	{ label: 'Gold crowns and bridges', iconSrc: '/images/calc/dental/what-we-buy/icon-crowns-bridges.svg' },
	{ label: 'Inlays and onlays', iconSrc: '/images/calc/dental/what-we-buy/icon-inlays-onlays.svg' },
	{ label: 'Gold dental scrap', iconSrc: '/images/calc/dental/what-we-buy/icon-dental-scrap.svg' },
	{ label: 'Partial dentures with gold framework', iconSrc: '/images/calc/dental/what-we-buy/icon-partial-dentures.svg' },
	{ label: 'Gold fillings, caps, and posts', iconSrc: '/images/calc/dental/what-we-buy/icon-fillings-caps.svg' },
	{ label: 'Laboratory dental scrap', iconSrc: '/images/calc/dental/what-we-buy/icon-lab-scrap.svg' },
]

const isDental = computed(() => props.pageVariant === 'dental')

const sectionTitle = computed(() =>
	isDental.value ? 'What Can You Sell?' : 'What We Buy',
)

const sectionIntro = computed(() =>
	isDental.value
		? 'We buy nearly all forms of dental gold, even items mixed with porcelain or other metals:'
		: 'We purchase nearly all forms of scrap gold, including:',
)

const footerNote = computed(() =>
	isDental.value
		? 'If you’re unsure what you have, include it in your mail-in kit. Our experts will sort and test every piece for free'
		: '',
)

const heroImage = computed(() =>
	isDental.value
		? {
				src: '/images/calc/dental/what-we-buy/hero.png',
				alt: 'Dental gold items including crowns, bridges, and scrap',
			}
		: {
				src: '/images/AdobeStock_532211983 2.png',
				alt: 'Assorted scrap gold jewelry and pieces',
			},
)

const items = computed(() => (isDental.value ? DENTAL_ITEMS : SCRAP_ITEMS))
</script>

<style scoped>
.gc-figma-391-912 {
	width: 100%;
	background: #fff;
	padding: 100px 0 80px;
	box-sizing: border-box;
}

.gc-figma-391-912__shell {
	max-width: 1530px;
	margin: 0 auto;
	padding: 0 0 0 24px;
	box-sizing: border-box;
}

.gc-figma-391-912__panel {
	background: #fbf5e8;
	border-radius: 0;
	max-width: min(1240px, 100%);
	width: 100%;
	margin-left: auto;
	margin-right: 0;
	padding: 48px 28px 56px 40px;
	box-sizing: border-box;
	overflow: visible;
}

.gc-figma-391-912__header {
	display: flex;
	flex-direction: column;
	gap: 20px;
	max-width: 649px;
	margin-bottom: 40px;
}

.gc-figma-391-912__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #000;
}

.gc-figma-391-912__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.333;
	color: #303030;
}

.gc-figma-391-912__footer {
	margin: 40px 0 0;
	max-width: 1117px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.4vw, 20px);
	line-height: 1.2;
	color: #303030;
}

.gc-figma-391-912__row {
	display: flex;
	flex-direction: row;
	align-items: center;
	gap: clamp(24px, 5vw, 96px);
}

.gc-figma-391-912__figure {
	flex: 0 0 auto;
	width: min(500px, 42vw);
	max-width: 100%;
	position: relative;
	z-index: 1;
}

.gc-figma-391-912__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 500 / 596;
	object-fit: cover;
	box-shadow: 10px 10px 0 0 #c39e3d;
	box-sizing: border-box;
}

.gc-figma-391-912__grid {
	list-style: none;
	margin: 0;
	padding: 0;
	flex: 1;
	min-width: 0;
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	column-gap: 12px;
	row-gap: 24px;
	align-content: start;
}

.gc-figma-391-912__cell {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 20px;
	min-width: 0;
}

.gc-figma-391-912__icon {
	display: flex;
	align-items: center;
	justify-content: flex-start;
	width: 40px;
	height: 40px;
	flex-shrink: 0;
}

.gc-figma-391-912__icon-img {
	display: block;
	width: 40px;
	height: 40px;
	object-fit: contain;
}

.gc-figma-391-912__label {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.6vw, 21px);
	line-height: 1.2;
	color: #000;
}

@media (min-width: 992px) {
	.gc-figma-391-912__figure {
		margin-left: calc(-1 * (40px + 28px));
	}
}

@media (min-width: 1200px) {
	.gc-figma-391-912__panel {
		max-width: min(1200px, 100%);
		padding: 60px 56px 80px 72px;
	}

	.gc-figma-391-912__figure {
		margin-left: calc(-1 * (72px + 36px));
	}
}

@media (max-width: 991px) {
	.gc-figma-391-912__shell {
		padding: 0 24px;
	}

	.gc-figma-391-912__panel {
		margin-left: 0;
		margin-right: 0;
		max-width: 100%;
	}

	.gc-figma-391-912__row {
		flex-direction: column;
		align-items: stretch;
	}

	.gc-figma-391-912__figure {
		width: 100%;
		max-width: 500px;
		margin-left: auto;
		margin-right: auto;
	}
}

@media (max-width: 600px) {
	.gc-figma-391-912__grid {
		grid-template-columns: 1fr;
	}

	.gc-figma-391-912__cell {
		flex-direction: row;
		align-items: center;
		gap: 16px;
	}

	.gc-figma-391-912__label {
		flex: 1;
		min-width: 0;
	}
}

@media (max-width: 767.98px) {
	.gc-figma-391-912__header {
		align-items: center;
		width: 100%;
		margin-left: auto;
		margin-right: auto;
		text-align: center;
	}

	.gc-figma-391-912__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-391-912__intro {
		font-size: 24px;
		line-height: 1.25;
		color: #dbaf3e;
	}

	.gc-figma-391-912__footer {
		margin-top: 32px;
		text-align: center;
	}
}
</style>
