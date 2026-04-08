<template>
	<section class="gold-calculator-hero" aria-labelledby="gold-calculator-hero-title">
		<div class="container">
			<div class="gold-calculator-hero__inner">
				<h1 id="gold-calculator-hero-title" class="gold-calculator-hero__title">
					{{ hero.title }}
				</h1>
				<p class="gold-calculator-hero__subtitle">
					{{ hero.subtitle }}
				</p>
				<p class="gold-calculator-hero__body">
					{{ hero.body }}
				</p>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const HERO_COPY = {
	calculator: {
		title: 'Gold Value Calculator',
		subtitle: 'Your gold has real value. Find out how much it’s worth today.',
		body:
			'Use our Gold Value Calculator to estimate the current market price of your gold based on purity (karat), weight, and current gold rates. Whether you’re selling jewelry, dental gold, coins, or scrap, Gold To Cash makes it fast, transparent, and secure to turn gold into cash.',
	},
	scrap: {
		title: 'Scrap Gold Calculator',
		subtitle: 'Find Out How Much Your Scrap Gold Is Worth',
		body:
			'Curious about what your broken jewelry, old coins, or scrap gold is really worth? Use our Scrap Gold Calculator to get an instant estimate based on current gold prices. Whether it’s a single earring or a tangled chain, your gold still holds real value - and Gold To Cash makes turning it into cash fast, secure, and transparent.',
	},
	dental: {
		title: 'Dental Gold Calculator',
		subtitle: 'Find Out How Much Your Scrap Gold Is Worth',
		body:
			'Ever wonder what those old gold crowns, bridges, or fillings are actually worth? Use our Dental Gold Value Calculator to estimate your payout based on the current value of gold at the time you calculate. Then, request your free mail-in appraisal kit to get a precise offer from our expert buyers backed by years of experience and transparent service.',
	},
}

const hero = computed(() => {
	const key = route.meta.faqVariant || 'calculator'
	return HERO_COPY[key] || HERO_COPY.calculator
})
</script>

<style scoped>
.gold-calculator-hero {
	padding: 100px 0 40px;
	background: #fff;
}

.gold-calculator-hero__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 30px 0;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	text-align: center;
}

.gold-calculator-hero__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.17;
	color: #000;
}

.gold-calculator-hero__subtitle {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1.125rem, 2.2vw, 1.5rem);
	line-height: 1.33;
	color: #dbaf3e;
}

.gold-calculator-hero__body {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #000;
	max-width: 100%;
}

@media (max-width: 767.98px) {
	.gold-calculator-hero {
		padding: 48px 0 40px;
	}

	.gold-calculator-hero__inner {
		padding: 16px 0;
	}

	.gold-calculator-hero__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gold-calculator-hero__subtitle {
		font-size: 24px;
		line-height: 1.25;
	}

	.gold-calculator-hero__body {
		font-size: 20px;
		line-height: 1.25;
	}
}
</style>
