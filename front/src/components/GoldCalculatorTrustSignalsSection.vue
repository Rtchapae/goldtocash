<template>
	<section
		class="gc-figma-374-427"
		:class="{ 'gc-figma-374-427--dental': pageVariant === 'dental' }"
		:data-figma-node="pageVariant === 'dental' ? '378-854' : '374-427'"
		aria-labelledby="gc-figma-374-427-heading"
	>
		<div class="gc-figma-374-427__inner">
			<header class="gc-figma-374-427__header">
				<h2 id="gc-figma-374-427-heading" class="gc-figma-374-427__title">
					{{ sectionTitle }}
				</h2>
				<p class="gc-figma-374-427__intro">
					{{ sectionIntro }}
				</p>
			</header>

			<div class="gc-figma-374-427__grid" role="list">
				<div class="gc-figma-374-427__col gc-figma-374-427__col--left" role="presentation">
					<div
						v-for="(item, i) in colLeft"
						:key="'l-' + i"
						class="gc-figma-374-427__item"
						role="listitem"
					>
						<img
							class="gc-figma-374-427__icon"
							:src="item.iconSrc"
							alt=""
							width="40"
							height="40"
							loading="lazy"
							decoding="async"
						/>
						<p class="gc-figma-374-427__item-text">{{ item.label }}</p>
					</div>
				</div>
				<div class="gc-figma-374-427__col gc-figma-374-427__col--right" role="presentation">
					<div
						v-for="(item, i) in colRight"
						:key="'r-' + i"
						class="gc-figma-374-427__item"
						role="listitem"
					>
						<img
							class="gc-figma-374-427__icon"
							:src="item.iconSrc"
							alt=""
							width="40"
							height="40"
							loading="lazy"
							decoding="async"
						/>
						<p class="gc-figma-374-427__item-text">{{ item.label }}</p>
					</div>
				</div>
			</div>

			<p class="gc-figma-374-427__footer">
				{{ footerText }}
			</p>
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

const isDental = computed(() => props.pageVariant === 'dental')

const sectionTitle = computed(() =>
	isDental.value ? 'Trust Built on Transparency' : 'We’re America’s Reliable Gold Buyers',
)

const sectionIntro = computed(() =>
	isDental.value
		? 'At Gold To Cash, we believe selling your dental gold should be straightforward and honest. Every appraisal is backed by:'
		: 'Every transaction with Gold To Cash is backed by honesty, precision, and protection:',
)

const footerText = computed(() =>
	isDental.value
		? 'You’ll know exactly what your items are worth and why.'
		: 'Your gold deserves to be valued fairly - and that’s exactly what we deliver.',
)

const SCRAP_COL_LEFT = [
	{
		label: 'Certified metal analysis and purity testing',
		iconSrc: '/images/calc/reliable-buyers/buyers-analysis.svg',
	},
	{
		label: 'Transparent documentation and recorded appraisals',
		iconSrc: '/images/calc/reliable-buyers/buyers-document.svg',
	},
]

const SCRAP_COL_RIGHT = [
	{
		label: 'Insured shipping both ways',
		iconSrc: '/images/calc/reliable-buyers/buyers-package.svg',
	},
	{
		label: 'No hidden deductions or surprise fees',
		iconSrc: '/images/calc/reliable-buyers/buyers-cash.svg',
	},
]

const DENTAL_COL_LEFT = [
	{
		label: 'Certified testing equipment for purity analysis',
		iconSrc: '/images/calc/dental/trust-signals/icon-certified-testing.svg',
	},
	{
		label: 'Accurate gold value calculation based on the price at the time of estimate',
		iconSrc: '/images/calc/dental/trust-signals/icon-accurate-pricing.svg',
	},
]

const DENTAL_COL_RIGHT = [
	{
		label: 'Video-recorded evaluations upon request',
		iconSrc: '/images/calc/dental/trust-signals/icon-video-evaluation.svg',
	},
	{
		label: 'No hidden fees or obligations',
		iconSrc: '/images/calc/dental/trust-signals/icon-no-hidden-fees.svg',
	},
]

const colLeft = computed(() => (isDental.value ? DENTAL_COL_LEFT : SCRAP_COL_LEFT))
const colRight = computed(() => (isDental.value ? DENTAL_COL_RIGHT : SCRAP_COL_RIGHT))
</script>

<style scoped>
.gc-figma-374-427 {
	width: 100%;
	background: #fff9ee;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-figma-374-427__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.gc-figma-374-427__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 15px;
	padding: 30px 0;
	box-sizing: border-box;
	text-align: center;
	width: 100%;
	max-width: 1228px;
}

.gc-figma-374-427__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #000;
}

.gc-figma-374-427__intro {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.333;
	color: #c39e3d;
}

.gc-figma-374-427--dental .gc-figma-374-427__intro {
	color: #000;
}

.gc-figma-374-427__grid {
	display: flex;
	flex-direction: row;
	flex-wrap: wrap;
	justify-content: center;
	align-items: flex-start;
	gap: 10px;
	width: 100%;
	list-style: none;
	margin: 0;
	padding: 0;
}

.gc-figma-374-427__col {
	display: flex;
	flex-direction: column;
	gap: 10px;
	box-sizing: border-box;
}

.gc-figma-374-427__col--left {
	flex: 0 1 570px;
	max-width: 570px;
}

.gc-figma-374-427__col--right {
	flex: 0 1 475px;
	max-width: 475px;
}

.gc-figma-374-427__item {
	display: flex;
	flex-direction: row;
	align-items: center;
	align-self: stretch;
	min-height: 98px;
	box-sizing: border-box;
	padding: 0 15px;
}

.gc-figma-374-427__col--left .gc-figma-374-427__item {
	padding-left: calc(95px + 15px);
}

.gc-figma-374-427__icon {
	flex: 0 0 auto;
	width: 40px;
	height: 40px;
	object-fit: contain;
	margin-right: 24px;
}

.gc-figma-374-427__item-text {
	margin: 0;
	flex: 1;
	min-width: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 24px);
	line-height: 1.333;
	color: #000;
}

.gc-figma-374-427__footer {
	margin: 0;
	width: 100%;
	max-width: 1228px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1.2;
	text-align: center;
	color: #000;
}

@media (max-width: 991px) {
	.gc-figma-374-427 {
		padding: 64px 0;
	}

	.gc-figma-374-427__inner {
		gap: 32px;
	}

	.gc-figma-374-427__grid {
		flex-direction: column;
		align-items: stretch;
	}

	.gc-figma-374-427__col--left,
	.gc-figma-374-427__col--right {
		max-width: none;
		flex: 1 1 auto;
	}

	.gc-figma-374-427__col--left .gc-figma-374-427__item {
		padding-left: 15px;
	}

	.gc-figma-374-427__item {
		min-height: 0;
		padding: 12px 15px;
	}
}

@media (max-width: 767.98px) {
	.gc-figma-374-427__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-374-427__intro {
		font-size: 24px;
		line-height: 1.25;
	}
}
</style>
