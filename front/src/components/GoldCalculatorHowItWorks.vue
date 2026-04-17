<template>
	<div class="calc-works-stack">
	<section class="calc-works" aria-labelledby="calc-works-heading">
		<div class="calc-works__inner">
			<header class="calc-works__header">
				<h2 id="calc-works-heading" class="calc-works__title">
					{{ calcWorksTitle }}
				</h2>
				<p
					class="calc-works__intro"
					:class="{ 'calc-works__intro--scrap': isScrapLayout }"
				>
					{{ calcWorksIntro }}
				</p>
			</header>

			<div class="calc-works__steps">
				<div class="calc-works__step">
					<p class="calc-works__step-label">step 1</p>
					<div class="calc-works__step-canvas" aria-hidden="true">
						<div class="calc-works__mock calc-works__mock--purity">
							<div class="calc-works__purity-clip">
								<div class="calc-works__purity-row">
									<div class="calc-works__pill calc-works__pill--chev calc-works__pill--chev-leading">
										<span class="calc-works__chev">▼</span>
									</div>
									<div class="calc-works__pill calc-works__pill--gold">
										<span class="calc-works__pill-gold-text">24 Karat</span>
										<span class="calc-works__mini-chev">▼</span>
									</div>
									<div class="calc-works__pill calc-works__pill--chev calc-works__pill--chev-plain" aria-hidden="true"></div>
								</div>
							</div>
						</div>
					</div>
					<p class="calc-works__step-text">
						Select your gold purity (10K, 14K, 18K, 22K, or 24K)
					</p>
				</div>

				<div class="calc-works__step">
					<p class="calc-works__step-label">step 2</p>
					<div class="calc-works__step-canvas calc-works__step-canvas--weight" aria-hidden="true">
						<div class="calc-works__mock calc-works__mock--weight">
							<div class="calc-works__input-fake">Enter weight</div>
						</div>
					</div>
					<p class="calc-works__step-text">
						Enter the weight in grams, ounces, or pennyweights.
					</p>
				</div>

				<div class="calc-works__step">
					<p class="calc-works__step-label">step 3</p>
					<div class="calc-works__step-canvas" aria-hidden="true">
						<div class="calc-works__mock calc-works__mock--pay">
							<div class="calc-works__pay-box">
								<span class="calc-works__pay-label">we will pay you</span>
								<span class="calc-works__pay-amount">$111.11</span>
							</div>
						</div>
					</div>
					<p class="calc-works__step-text">
						Instantly see your estimated gold value in U.S. dollars.
					</p>
				</div>
			</div>

			<p class="calc-works__footnote">
				Your quote will update automatically as the gold market price changes - giving you a real-time snapshot of your
				gold’s potential value before you mail it in for a free appraisal.
			</p>
		</div>
	</section>
	<GoldCalculatorWhyChooseSection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorWhatWeBuySection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorProcessStepsSection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorGoldValueMessageSection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorAccuracyMattersSection v-if="isScrapLayout" />
	<GoldCalculatorTrustSignalsSection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorSellCtaSection v-if="isScrapLayout" :page-variant="faqVariant" />
	<GoldCalculatorGoldTypes v-if="!isScrapLayout" />
	<GoldCalculatorTabbedTools v-if="!isScrapLayout" />
	<GoldCalculatorCalcPay v-if="!isScrapLayout" />
	<GoldCalculatorGoldValueFactors v-if="!isScrapLayout" />
	<GoldCalculatorWhyUse v-if="!isScrapLayout" />
	<GoldCalculatorUseBlock v-if="!isScrapLayout" variant="bottom" />
	<GoldCalculatorFaq :variant="faqVariant" />
	</div>
</template>

<script setup>
import { computed } from 'vue'
import GoldCalculatorWhyChooseSection from './GoldCalculatorWhyChooseSection.vue'
import GoldCalculatorWhatWeBuySection from './GoldCalculatorWhatWeBuySection.vue'
import GoldCalculatorProcessStepsSection from './GoldCalculatorProcessStepsSection.vue'
import GoldCalculatorGoldValueMessageSection from './GoldCalculatorGoldValueMessageSection.vue'
import GoldCalculatorAccuracyMattersSection from './GoldCalculatorAccuracyMattersSection.vue'
import GoldCalculatorTrustSignalsSection from './GoldCalculatorTrustSignalsSection.vue'
import GoldCalculatorSellCtaSection from './GoldCalculatorSellCtaSection.vue'
import GoldCalculatorGoldTypes from './GoldCalculatorGoldTypes.vue'
import GoldCalculatorTabbedTools from './GoldCalculatorTabbedTools.vue'
import GoldCalculatorCalcPay from './GoldCalculatorCalcPay.vue'
import GoldCalculatorGoldValueFactors from './GoldCalculatorGoldValueFactors.vue'
import GoldCalculatorWhyUse from './GoldCalculatorWhyUse.vue'
import GoldCalculatorUseBlock from './GoldCalculatorUseBlock.vue'
import GoldCalculatorFaq from './GoldCalculatorFaq.vue'

const props = defineProps({
	faqVariant: {
		type: String,
		default: 'calculator',
		validator: (v) => ['calculator', 'scrap', 'dental'].includes(v),
	},
})

const isScrapLayout = computed(() => props.faqVariant === 'scrap' || props.faqVariant === 'dental')

const calcWorksTitle = computed(() => {
	if (props.faqVariant === 'scrap') return 'How We Calculate Scrap Gold Value'
	if (props.faqVariant === 'dental') return 'How Dental Gold Value Is Calculated'
	return 'How The Gold Value Calculator Works'
})

const calcWorksIntro = computed(() => {
	if (props.faqVariant === 'scrap') {
		return 'We combine live gold spot pricing with the purity and weight you enter to estimate melt value for scrap jewelry and mixed pieces.'
	}
	if (props.faqVariant === 'dental') {
		return 'We combine live gold spot pricing with the purity and weight you enter to estimate melt value for dental crowns, bridges, and alloys.'
	}
	return 'Our calculator uses current market data and the spot price of gold per gram to estimate what your gold is worth.'
})
</script>

<style scoped>
.calc-works-stack {
	width: 100%;
}

.calc-works {
	padding: 100px 0;
	background: #fff;
	width: 100%;
}

.calc-works__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.calc-works__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1228px;
	text-align: center;
}

.calc-works__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.17;
	color: #000;
}

.calc-works__intro {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 2vw, 1.25rem);
	line-height: 1.2;
	color: #303030;
}

.calc-works__steps {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 20px;
	width: 100%;
	max-width: 1228px;
	align-items: start;
}

.calc-works__step {
	position: relative;
	background: #fff;
	border-radius: 20px;
	box-shadow: 0 -4px 10px rgba(0, 0, 0, 0.2), 0 4px 10px rgba(0, 0, 0, 0.2);
	padding: 30px 0px 18px;
	height: 400px;
	width: 100%;
	max-width: 396px;
	margin: 0 auto;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	justify-content: flex-start;
	box-sizing: border-box;
	overflow: hidden;
}

.calc-works__step-label {
	margin: 0;
	flex-shrink: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: clamp(1.125rem, 2.2vw, 2rem);
	line-height: 1;
	text-transform: uppercase;
	letter-spacing: 0.02em;
	color: #000;
	text-align: center;
}

.calc-works__step-canvas {
	flex: 1;
	min-height: 0;
	width: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	overflow: hidden;
	margin-top: 12px;
}

.calc-works__step-canvas--weight {
	align-items: flex-start;
	justify-content: flex-start;
	padding-top: 0;
	margin-top: 0;
}

.calc-works__step-text {
	margin: 0;
	margin-top: auto;
	padding-top: 12px;
	flex-shrink: 0;
	font-family: Montserrat, sans-serif;
	font-weight: 500;
	font-size: clamp(0.9375rem, 1.6vw, 1.5rem);
	line-height: 1.333;
	color: #000;
	text-align: center;
}

.calc-works__mock {
	position: relative;
	width: 100%;
}

/* Wider than card, centered — gold pill stays visually centered; chevrons clip at sides */
.calc-works__mock--purity {
	padding-bottom: 2px;
}

.calc-works__purity-clip {
	overflow: hidden;
	width: 100%;
}

.calc-works__purity-row {
	display: flex;
	flex-direction: row;
	flex-wrap: nowrap;
	justify-content: center;
	align-items: stretch;
	gap: 10px;
	width: 138%;
	min-width: 340px;
	margin-left: 50%;
	transform: translateX(-50%);
	box-sizing: border-box;
}

.calc-works__pill {
	display: flex;
	align-items: center;
	justify-content: center;
	min-height: 52px;
	padding: 0 10px;
	border-radius: 17px;
	box-sizing: border-box;
	flex-shrink: 0;
}

.calc-works__pill--chev {
	width: 100px;
	min-width: 100px;
	border: 1.43px solid #cecece;
	color: #767676;
}

.calc-works__pill--chev-leading {
	justify-content: flex-end;
	padding-right: 14px;
	padding-left: 6px;
}

.calc-works__pill--chev-plain {
	pointer-events: none;
}

.calc-works__pill--gold {
	width: 196px;
	min-width: 196px;
	padding: 0 16px;
	background: rgba(219, 175, 62, 0.2);
	justify-content: space-between;
	gap: 8px;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: clamp(0.8125rem, 2.5vw, 1.125rem);
	line-height: 1.07;
	color: #000;
}

.calc-works__pill-gold-text {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	min-width: 0;
}

.calc-works__chev,
.calc-works__mini-chev {
	font-size: 12px;
	line-height: 1;
	flex-shrink: 0;
}

/* Step 2: nudged down + slightly more right */
.calc-works__mock--weight {
	width: 92%;
	max-width: 320px;
	margin-left: calc(17.333% + 14px);
	margin-right: -20px;
	margin-top: 45px;
	transform: none;
}

.calc-works__input-fake {
	width: 100%;
	min-height: 56px;
	padding: 14px 18px;
	border-radius: 15px;
	background: rgba(0, 0, 0, 0.1);
	border: 1.24px solid #c39e3d;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: clamp(0.875rem, 2.5vw, 1.25rem);
	line-height: 1.2;
	color: #c39e3d;
	text-align: left;
	display: flex;
	align-items: center;
	justify-content: flex-start;
	box-sizing: border-box;
}

/* Step 3: pay strip — slightly larger, nudged up in canvas */
.calc-works__mock--pay {
	width: 132%;
	margin-left: 50%;
	transform: translate(-50%, -10px);
}

.calc-works__pay-box {
	width: 100%;
	min-width: 0;
	padding: 22px 20px 20px;
	border-radius: 16px;
	background: #fff;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	justify-content: center;
	gap: 12px;
	box-sizing: border-box;
}

.calc-works__pay-label {
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: clamp(0.8125rem, 2.5vw, 1.125rem);
	line-height: 1.3;
	letter-spacing: 0.02em;
	text-transform: uppercase;
	color: #424242;
	text-align: left;
	white-space: nowrap;
}

.calc-works__pay-amount {
	font-family: Montserrat, sans-serif;
	font-weight: 700;
	font-size: clamp(2.35rem, 5.25vw, 4.25rem);
	line-height: 1;
	color: #000;
	text-align: center;
}

@media (min-width: 992px) {
	.calc-works__purity-row {
		gap: 12px;
		min-width: 380px;
	}

	.calc-works__pill {
		min-height: 60px;
		padding: 0 12px;
		border-radius: 18px;
	}

	.calc-works__pill--chev {
		width: 118px;
		min-width: 118px;
	}

	.calc-works__pill--gold {
		width: 228px;
		min-width: 228px;
		min-height: 60px;
		padding: 0 18px;
		font-size: 1.125rem;
	}

	.calc-works__chev,
	.calc-works__mini-chev {
		font-size: 14px;
	}

	.calc-works__input-fake {
		min-height: 64px;
		padding: 16px 20px;
		border-radius: 16px;
		font-size: 1.125rem;
	}

	.calc-works__mock--pay {
		width: 142%;
	}

	.calc-works__pay-box {
		padding: 30px 26px 26px;
		gap: 14px;
		border-radius: 18px;
	}

	.calc-works__pay-label {
		font-size: 1.1875rem;
	}

	.calc-works__pay-amount {
		font-size: clamp(3.25rem, 3.85vw, 5rem);
	}

	.calc-works__mock--weight {
		max-width: 340px;
	}
}

.calc-works__footnote {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 2vw, 1.25rem);
	line-height: 1.2;
	text-align: center;
	color: #555;
}

@media (max-width: 991.98px) {
	.calc-works {
		padding: 60px 0 80px;
	}

	.calc-works__steps {
		grid-template-columns: 1fr;
		max-width: 440px;
	}

	.calc-works__step {
		height: auto;
		min-height: 360px;
		max-width: 100%;
	}

	.calc-works__purity-row {
		width: 130%;
		min-width: 0;
	}
}

@media (max-width: 767.98px) {
	.calc-works__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.calc-works__intro {
		font-size: 24px;
		line-height: 1.2;
	}

	.calc-works__intro--scrap {
		color: #c39e3d;
	}

	.calc-works__inner {
		padding-left: max(12px, env(safe-area-inset-left));
		padding-right: max(12px, env(safe-area-inset-right));
	}

	.calc-works__steps {
		gap: 14px;
		max-width: min(340px, 92vw);
		margin-left: auto;
		margin-right: auto;
	}

	.calc-works__step {
		padding: 10px 8px 8px;
		min-height: 0;
		height: auto;
		width: 100%;
		max-width: min(340px, 92vw);
		margin-left: auto;
		margin-right: auto;
		aspect-ratio: 1;
		overflow: hidden;
	}

	.calc-works__step-label {
		font-size: 32px;
		line-height: 1.1;
	}

	.calc-works__step-canvas {
		margin-top: 6px;
	}

	.calc-works__step-canvas--weight {
		margin-top: 0;
	}

	.calc-works__mock--weight {
		width: 100%;
		max-width: 280px;
		margin-left: auto;
		margin-right: auto;
		margin-top: 20px;
	}

	.calc-works__mock--pay {
		width: 100%;
		transform: translate(0, -4px);
		margin-left: 0;
	}

	.calc-works__pay-amount {
		font-size: clamp(3.25rem, 13vw, 4.75rem);
	}

	.calc-works__pay-label {
		font-size: clamp(0.875rem, 3.4vw, 1.125rem);
	}

	.calc-works__step-text {
		font-size: 1.5rem;
		line-height: 1.33;
		padding-top: 8px;
	}

	.calc-works__footnote {
		font-size: 1.25rem;
		line-height: 1.35;
	}

	.calc-works__pill--chev {
		width: 110px;
		min-width: 110px;
		min-height: 58px;
	}

	.calc-works__pill--gold {
		width: 216px;
		min-width: 216px;
		min-height: 64px;
		padding: 0 18px;
		font-size: clamp(1.125rem, 4.6vw, 1.375rem);
	}

	.calc-works__chev,
	.calc-works__mini-chev {
		font-size: 15px;
	}

	.calc-works__input-fake {
		min-height: 62px;
		font-size: clamp(1.1875rem, 4.6vw, 1.5rem);
		padding: 18px 20px;
	}

	.calc-works__pay-box {
		padding: 24px 18px 18px;
		gap: 14px;
	}
}
</style>
