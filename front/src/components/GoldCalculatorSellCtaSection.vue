<template>
	<section
		class="gc-figma-507-899"
		:class="{
			'gc-figma-507-899--dental': pageVariant === 'dental',
			'gc-figma-507-899--how-sell': pageVariant === 'how-sell',
		}"
		:data-figma-node="pageVariant === 'dental' ? '501-695' : '507-899'"
		aria-labelledby="gc-figma-507-899-heading"
	>
		<div class="gc-figma-507-899__inner">
			<div class="gc-figma-507-899__row">
				<div class="gc-figma-507-899__copy">
					<h2 id="gc-figma-507-899-heading" class="gc-figma-507-899__title">
						{{ title }}
					</h2>
					<div class="gc-figma-507-899__texts">
						<p class="gc-figma-507-899__p">
							{{ paragraphFirst }}
						</p>
						<p class="gc-figma-507-899__p">
							{{ paragraphSecond }}
						</p>
					</div>
				</div>
				<figure class="gc-figma-507-899__figure">
					<img
						class="gc-figma-507-899__img"
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
		validator: (v) => ['scrap', 'dental', 'calculator', 'how-sell'].includes(v),
	},
})

const isDental = computed(() => props.pageVariant === 'dental')
const isHowSell = computed(() => props.pageVariant === 'how-sell')

const title = computed(() =>
	isDental.value
		? 'Turn Your Dental Gold Into Real Value'
		: 'Turn Your Scrap Gold Into Cash Today',
)

const paragraphFirst = computed(() => {
	if (isDental.value) {
		return 'Old dental work shouldn’t collect dust in a drawer. Even small pieces can add up fast.'
	}
	if (isHowSell.value) {
		return 'Whether it’s broken jewelry or forgotten keepsakes, your gold still holds value. With the current gold market, it may be a good time to capitalize on your gold items.'
	}
	return 'Whether you have broken chains, mismatched earrings, or leftover scraps from a project, our calculator helps you see what your gold could be worth at today’s spot prices.'
})

const paragraphSecond = computed(() => {
	if (isDental.value) {
		return 'Get an estimate today using our calculator, then request your free appraisal kit to find out your exact payout - fast, secure, and trusted by thousands of sellers nationwide.'
	}
	if (isHowSell.value) {
		return 'If you’d like to join the thousands of Gold to Cash patrons, simply get a real-time estimate with our Scrap Gold Calculator, then fill out the form to request your free mail-in kit to receive a professional appraisal and fast payment. It’s free, reliable, and risk-free.'
	}
	return 'Get an estimate in seconds, then request a free appraisal kit for an official offer from Gold To Cash.'
})

const hero = computed(() => {
	if (isDental.value) {
		return {
			src: '/images/calc/dental/sell-cta/hero.png',
			alt: 'Dental gold and restorations ready to value and sell',
		}
	}
	if (isHowSell.value) {
		return {
			src: '/images/how-sell-gold/cta-hero.webp',
			alt: 'Gold jewelry ready to sell for cash',
		}
	}
	return {
		src: '/images/image 3.png',
		alt: 'Gold coins and scrap gold ready to sell',
	}
})
</script>

<style scoped>
.gc-figma-507-899 {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-figma-507-899__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 50px;
}

.gc-figma-507-899__row {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	gap: 47px;
	box-sizing: border-box;
}

.gc-figma-507-899__figure {
	flex: 0 0 auto;
	width: min(604px, 48%);
	max-width: 604px;
	margin: 0;
	box-shadow:
		10px 10px 0 0 #c39e3d,
		10px 10px 4px 0 rgba(0, 0, 0, 0.25);
	box-sizing: border-box;
}

.gc-figma-507-899--dental .gc-figma-507-899__figure {
	box-shadow:
		10px 10px 0 0 #c39e3d,
		4px 4px 10px 0 rgba(0, 0, 0, 0.25);
}

/* Dental desktop: image left, copy right (mobile keeps copy-first in DOM). */
@media (min-width: 992px) {
	.gc-figma-507-899--dental .gc-figma-507-899__figure {
		order: 1;
	}

	.gc-figma-507-899--dental .gc-figma-507-899__copy {
		order: 2;
	}
}

.gc-figma-507-899__img {
	display: block;
	width: 100%;
	height: auto;
	aspect-ratio: 604 / 440;
	object-fit: cover;
}

/* Dental hero is square; show full image inside the 604×440 frame. */
.gc-figma-507-899--dental .gc-figma-507-899__img {
	object-fit: contain;
	object-position: center;
}

.gc-figma-507-899__copy {
	flex: 1 1 577px;
	min-width: 0;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 30px;
}

.gc-figma-507-899__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #000;
}

.gc-figma-507-899__texts {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 20px;
}

.gc-figma-507-899__p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1.2;
	color: #000;
}

@media (max-width: 991px) {
	.gc-figma-507-899 {
		padding: 64px 0;
	}

	.gc-figma-507-899__row {
		flex-direction: column;
		align-items: stretch;
		gap: 40px;
	}

	.gc-figma-507-899__figure {
		width: 100%;
		max-width: 500px;
		margin: 0 auto;
	}

	.gc-figma-507-899__copy {
		flex: 1 1 auto;
		max-width: 100%;
	}

	/* How-sell mobile (Figma 641:1362): image first, centered copy */
	.gc-figma-507-899--how-sell .gc-figma-507-899__figure {
		order: -1;
	}

	.gc-figma-507-899--how-sell .gc-figma-507-899__title,
	.gc-figma-507-899--how-sell .gc-figma-507-899__p {
		text-align: center;
	}
}

@media (max-width: 767.98px) {
	.gc-figma-507-899__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-507-899__p {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-figma-507-899--how-sell .gc-figma-507-899__p {
		font-size: 16px;
		line-height: 1.45;
	}
}
</style>
