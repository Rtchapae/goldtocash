<template>
	<section class="gc-calculator-embed" data-section="calculator-embed" :aria-label="sectionAriaLabel">
		<div class="gc-calculator-embed__inner">
			<div class="gc-calculator-embed__grid">
				<div class="gc-calculator-embed__copy">
					<h2 class="gc-calculator-embed__title">Use The Calculator Below</h2>
					<p v-for="(lead, i) in leadParagraphs" :key="i" class="gc-calculator-embed__lead">
						{{ lead }}
					</p>
				</div>
				<div class="gc-calculator-embed__calc-wrap">
					<div class="gc-calculator-embed__panel">
						<ClientOnly>
							<ValueCalculator
								variant="tabbed"
								:heading="calcHeading"
								:field-id-prefix="fieldIdPrefix"
							/>
						</ClientOnly>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { computed } from 'vue'
import ClientOnly from '@/components/ClientOnly.vue'
import ValueCalculator from '@/components/ValueCalculator.vue'

const props = defineProps({
	/** Matches /scrap-gold-calculator vs /dental-gold-calculator (copy + calculator labels only). */
	variant: {
		type: String,
		default: 'scrap',
		validator: (v) => ['scrap', 'dental'].includes(v),
	},
})

const sectionAriaLabel = computed(() =>
	props.variant === 'dental' ? 'Dental gold value calculator' : 'Scrap gold value calculator'
)

const calcHeading = computed(() =>
	props.variant === 'dental' ? 'Dental Gold Calculator' : 'Scrap Gold Calculator'
)

const fieldIdPrefix = computed(() =>
	props.variant === 'dental' ? 'dental-calculator-embed' : 'scrap-calculator-embed'
)

const leadParagraphs = computed(() => {
	if (props.variant === 'dental') {
		return [
			'Enter your gold’s weight and karat to estimate its melt value. The calculator uses the gold price at the time of your calculation to provide an accurate estimate.',
			'If you’re not sure about purity or weight, don’t worry, our mail-in kit includes a professional evaluation, free of charge.',
		]
	}
	return [
		'Enter your gold’s weight and purity (karat) to see its estimated melt value in real time. Our calculator pulls data from the current gold market, so your estimate is always up to date.',
		'Don’t worry if you don’t know your gold’s purity. When you send it in, our certified experts will test each piece and confirm the exact karat before making an offer.',
	]
})
</script>

<style scoped>
/* Inline calculator on scrap/dental routes: matches tabbed tools panel styling. */
.gc-calculator-embed {
	width: 100%;
	background: #c39e3d4d;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-calculator-embed__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.gc-calculator-embed__grid {
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(0, 520px);
	align-items: start;
	gap: 48px 56px;
}

.gc-calculator-embed__copy {
	min-width: 0;
	padding-top: 8px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.gc-calculator-embed__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	color: #131615;
}

.gc-calculator-embed__lead {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #000;
	max-width: 560px;
}

.gc-calculator-embed__calc-wrap {
	min-width: 0;
	width: 100%;
}

.gc-calculator-embed__panel {
	width: 100%;
	background: #fff;
	border: 1px solid #000;
	border-radius: 20px;
	box-shadow: 6px 6px 0 0 #000;
	padding: 24px 20px 28px;
	box-sizing: border-box;
}

.gc-calculator-embed__panel :deep(.vc-figma) {
	margin: 0;
}

@media (min-width: 992px) {
	.gc-calculator-embed__title {
		font-size: 48px;
		line-height: 56px;
	}
}

@media (max-width: 991.98px) {
	.gc-calculator-embed {
		padding: 60px 0 80px;
	}

	.gc-calculator-embed__grid {
		grid-template-columns: 1fr;
		gap: 36px;
	}

	.gc-calculator-embed__lead {
		max-width: none;
		text-align: center;
	}

	.gc-calculator-embed__title {
		text-align: center;
	}

	.gc-calculator-embed__panel {
		box-shadow: 4px 4px 0 0 #000;
		max-width: 560px;
		margin: 0 auto;
	}
}

@media (max-width: 767.98px) {
	.gc-calculator-embed__inner {
		padding: 0 16px;
	}

	.gc-calculator-embed__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-calculator-embed__lead {
		font-size: 20px;
		line-height: 1.25;
	}

	.gc-calculator-embed__panel {
		padding: 20px 16px 24px;
	}
}
</style>
