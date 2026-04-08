<template>
	<section
		class="gc-calculator-use"
		:class="{ 'gc-calculator-use--bottom': isBottom }"
		:aria-labelledby="headingId"
	>
		<div class="gc-calculator-use__head" :class="{ 'gc-calculator-use__head--bottom-cta': isBottom }">
			<h2 v-if="!isBottom" :id="headingId" class="gc-calculator-use__title">
				Use The Calculator Below
			</h2>
			<h2 v-else :id="headingId" class="gc-calculator-use__title gc-calculator-use__title--bottom">
				Ready To Discover What<br />
				Your Gold Is Worth?
			</h2>
			<p v-if="isBottom" class="gc-calculator-use__subtitle">
				It only takes a minute to get started. Use our calculator to estimate your gold's value, then request your
				free appraisal kit for an official offer.
			</p>
		</div>
		<div class="container gc-calculator-use__calc">
			<div class="gc-calculator-use__calc-slot">
				<ClientOnly>
					<ValueCalculator />
				</ClientOnly>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import ValueCalculator from '@/components/ValueCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'

const props = defineProps({
	/** 'default' = top of page. 'bottom' = Figma 62-1651 heading + body copy. */
	variant: {
		type: String,
		default: 'default',
		validator: (v) => ['default', 'bottom'].includes(v),
	},
})

const isBottom = computed(() => props.variant === 'bottom')
const headingId = computed(() =>
	isBottom.value ? 'gc-calculator-use-bottom-heading' : 'gc-calculator-use-heading'
)
</script>

<style scoped>
.gc-calculator-use {
	background: #fbf5e8;
	padding: 24px 0 48px;
	box-sizing: border-box;
}

/* Top block: matches calculator hero title scale (48px desktop) */
.gc-calculator-use:not(.gc-calculator-use--bottom) .gc-calculator-use__head {
	max-width: 1228px;
	margin: 0 auto 28px;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	text-align: center;
}

.gc-calculator-use:not(.gc-calculator-use--bottom) .gc-calculator-use__title {
	margin: 0;
	width: 100%;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	letter-spacing: 0;
	text-align: center;
	color: #000;
}

@media (min-width: 992px) {
	.gc-calculator-use:not(.gc-calculator-use--bottom) .gc-calculator-use__title {
		font-size: 48px;
		line-height: 56px;
	}
}

.gc-calculator-use--bottom {
	padding: 100px 0 48px;
}

.gc-calculator-use__head--bottom-cta {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	margin: 0 auto 28px;
	box-sizing: border-box;
	text-align: center;
}

/* Bottom CTA: h2 — 48px / 56px, Montserrat Bold, centered */
.gc-calculator-use__title--bottom {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	letter-spacing: 0;
	text-align: center;
	color: #000;
}

@media (min-width: 992px) {
	.gc-calculator-use__title--bottom {
		font-size: 48px;
		line-height: 56px;
	}
}

/* Bottom CTA: subtitle — 20px / 24px, Montserrat Regular, centered */
.gc-calculator-use--bottom .gc-calculator-use__subtitle {
	margin: 0;
	max-width: 883px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 2vw, 20px);
	line-height: 24px;
	letter-spacing: 0;
	text-align: center;
	color: #000;
}

@media (min-width: 992px) {
	.gc-calculator-use--bottom .gc-calculator-use__subtitle {
		font-size: 20px;
	}
}

@media (max-width: 767.98px) {
	.gc-calculator-use:not(.gc-calculator-use--bottom) .gc-calculator-use__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-calculator-use__title--bottom {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-calculator-use--bottom .gc-calculator-use__subtitle {
		font-size: 24px;
		line-height: 1.25;
	}
}

.gc-calculator-use__calc {
	margin-bottom: 80px;
	min-width: 0;
	max-width: 100%;
	box-sizing: border-box;
}

.gc-calculator-use__calc-slot {
	min-width: 0;
	max-width: 100%;
}

.gc-calculator-use__calc :deep(.vc-figma) {
	min-width: 0;
	max-width: 100%;
}
</style>
