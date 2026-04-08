<template>
	<section
		class="gc-figma-375-583"
		:class="{ 'gc-figma-375-583--dental': pageVariant === 'dental' }"
		:data-figma-node="pageVariant === 'dental' ? '501-666' : '375-583'"
		aria-labelledby="gc-figma-375-583-heading"
	>
		<div class="gc-figma-375-583__inner">
			<div class="gc-figma-375-583__row">
				<div class="gc-figma-375-583__copy">
					<h2 id="gc-figma-375-583-heading" class="gc-figma-375-583__title">
						{{ title }}
					</h2>
					<div class="gc-figma-375-583__texts">
						<p class="gc-figma-375-583__lead">
							{{ leadText }}
						</p>
						<p class="gc-figma-375-583__sub">
							{{ subText }}
						</p>
					</div>
				</div>
				<figure class="gc-figma-375-583__figure">
					<img
						class="gc-figma-375-583__img"
						:src="hero.src"
						width="604"
						height="440"
						loading="lazy"
						decoding="async"
						:alt="hero.alt"
					/>
				</figure>
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

const isDental = computed(() => props.pageVariant === 'dental')

const title = computed(() =>
	isDental.value
		? 'Understanding Dental Gold Purity'
		: 'Know the Real Value of Your Gold',
)

const leadText = computed(() =>
	isDental.value
		? 'Most dental gold alloys fall between 10 karat (41.7% gold) and 22 karat (91.6% gold). Older dental restorations often contain higher percentages of gold than modern alloys, which use more base metals for durability.'
		: 'Gold prices change every day. Even small pieces of broken jewelry can add up to hundreds of dollars. Our calculator helps you stay informed and confident about what your gold is worth before you sell.',
)

const subText = computed(() =>
	isDental.value
		? 'If you’re not sure what karat your gold is, our in-house testing identifies exact purity levels before we calculate your offer.'
		: 'At Gold To Cash, we make sure the process is simple, transparent, and rewarding from start to finish.',
)

const hero = computed(() =>
	isDental.value
		? {
				src: '/images/calc/dental/gold-purity/hero.png',
				alt: 'Dental gold samples and restorations for purity evaluation',
			}
		: {
				src: '/images/AdobeStock_532211983 1.png',
				alt: 'Gold jewelry and scrap gold pieces',
			},
)
</script>

<style scoped>
.gc-figma-375-583 {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-figma-375-583__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 50px;
}

.gc-figma-375-583__row {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	gap: 47px;
	box-sizing: border-box;
}

.gc-figma-375-583__copy {
	flex: 1 1 577px;
	min-width: 0;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 30px;
}

.gc-figma-375-583__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #000;
}

.gc-figma-375-583__texts {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 20px;
}

.gc-figma-375-583__lead {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1.2;
	color: #000;
}

.gc-figma-375-583__sub {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1;
	color: #000;
}

.gc-figma-375-583--dental .gc-figma-375-583__sub {
	line-height: 1.2;
}

.gc-figma-375-583__figure {
	flex: 0 0 auto;
	width: min(604px, 48%);
	max-width: 604px;
	margin: 0;
	box-shadow:
		10px 10px 0 0 #c39e3d,
		10px 10px 4px 0 rgba(0, 0, 0, 0.25);
	box-sizing: border-box;
}

.gc-figma-375-583--dental .gc-figma-375-583__figure {
	box-shadow:
		10px 10px 0 0 #c39e3d,
		4px 4px 10px 0 rgba(0, 0, 0, 0.25);
}

.gc-figma-375-583__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 604 / 440;
	object-fit: cover;
}

@media (max-width: 991px) {
	.gc-figma-375-583 {
		padding: 64px 0;
	}

	.gc-figma-375-583__row {
		flex-direction: column;
		align-items: stretch;
		gap: 40px;
	}

	.gc-figma-375-583__copy {
		flex: 1 1 auto;
		max-width: 100%;
	}

	.gc-figma-375-583__figure {
		width: 100%;
		max-width: 500px;
		margin: 0 auto;
	}
}

@media (max-width: 767.98px) {
	.gc-figma-375-583__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-375-583__lead,
	.gc-figma-375-583__sub {
		font-size: 24px;
		line-height: 1.25;
	}
}
</style>
