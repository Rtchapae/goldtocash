<template>
	<section class="wwp-calc" aria-labelledby="wwp-calc-title">
		<div class="wwp-calc__inner">
			<header class="wwp-calc__header">
				<h2 id="wwp-calc-title" class="wwp-calc__title">Curious What Your gold may be worth?</h2>
				<p class="wwp-calc__lead">
					We have a quick and easy way to get a number with our helpful Gold Calculator. Use our Gold Value
					Calculator to estimate the current market price of your gold based on purity (karat), weight, and current
					gold rates. Whether you're selling jewelry, dental gold, coins, or scrap, Gold To Cash makes it fast,
					transparent, and secure to turn gold into cash.
				</p>
			</header>
			<div class="wwp-calc__widget">
				<ClientOnly>
					<ValueCalculator />
				</ClientOnly>
			</div>
		</div>
	</section>
</template>

<script setup>
import ClientOnly from '@/components/ClientOnly.vue'
import ValueCalculator from '@/components/ValueCalculator.vue'

/** Figma 1026:1793 / 1220:1789 — reuse existing calculator */
</script>

<style scoped>
.wwp-calc {
	width: 100%;
	background: #000;
	padding: 80px 0 100px;
	box-sizing: border-box;
}

.wwp-calc__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.wwp-calc__header {
	max-width: 900px;
	text-align: center;
	display: flex;
	flex-direction: column;
	gap: 16px;
}

.wwp-calc__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.5vw, 40px);
	line-height: 1.2;
	color: #c39e3d;
}

.wwp-calc__lead {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 18px);
	line-height: 1.5;
	color: #fff;
}

.wwp-calc__widget {
	width: 100%;
	max-width: 1100px;
}

@media (max-width: 767.98px) {
	.wwp-calc {
		padding: 56px 0 64px;
	}

	.wwp-calc__inner {
		gap: 28px;
	}
}
</style>
