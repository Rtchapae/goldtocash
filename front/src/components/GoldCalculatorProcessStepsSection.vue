<template>
	<section
		class="gc-figma-346-371"
		:class="{ 'gc-figma-346-371--dental': pageVariant === 'dental' }"
		data-figma-node="346-371"
		aria-labelledby="gc-figma-346-371-heading"
	>
		<div class="gc-figma-346-371__inner">
			<header class="gc-figma-346-371__header">
				<h2 id="gc-figma-346-371-heading" class="gc-figma-346-371__title">How It Works</h2>
			</header>

			<ol class="gc-figma-346-371__steps" role="list">
				<li v-for="(step, i) in steps" :key="i" class="gc-figma-346-371__step">
					<p class="gc-figma-346-371__num" aria-hidden="true">{{ step.num }}</p>
					<div class="gc-figma-346-371__body">
						<h3 class="gc-figma-346-371__step-title">
							<span class="gc-figma-346-371__step-title-lines">
								<template v-for="(line, li) in step.titleLines" :key="'d-' + li">
									<template v-if="li > 0"><br /></template>
									{{ line }}
								</template>
							</span>
							<span class="gc-figma-346-371__step-title-one">{{ step.titleLines.join(' ') }}</span>
						</h3>
						<p class="gc-figma-346-371__step-text">{{ step.body }}</p>
					</div>
				</li>
			</ol>
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

const SCRAP_STEPS = [
	{
		num: '01',
		titleLines: ['Request', 'Your Free Appraisal Kit'],
		body: "fill out the short form on this page. We'll mail you a secure, prepaid envelope insured for up to $5,000",
	},
	{
		num: '02',
		titleLines: ['Ship Your Scrap', 'Gold'],
		body: 'Pack your gold items securely and drop the envelope at any USPS location',
	},
	{
		num: '03',
		titleLines: ['Get a Professional Evaluation'],
		body: 'Our appraisers weigh and test each piece using calibrated, industry-grade equipment',
	},
	{
		num: '04',
		titleLines: ['Receive Your Offer Within 24 Hours'],
		body: "We base our payout on the up-to-date gold price and your gold's purity with no hidden fees.",
	},
	{
		num: '05',
		titleLines: ['Accept and Get Paid'],
		body: 'Approve your offer and get paid instantly via check, PayPal, or bank transfer',
	},
]

const DENTAL_STEPS = [
	{
		num: '01',
		titleLines: ['Request', 'Your Free Appraisal Kit'],
		body: "fill out the short form on this page. We'll mail you a secure, prepaid envelope insured for up to $5,000",
	},
	{
		num: '02',
		titleLines: ['Send Us Your Dental Gold'],
		body: 'Pack your items securely and drop them at any USPS location.',
	},
	{
		num: '03',
		titleLines: ['Get an Offer Within', '24 Hours'],
		body: 'Our appraisers test, weigh, and value your items using precise gold testing methods.',
	},
	{
		num: '04',
		titleLines: ['Accept and Get Paid Fast'],
		body: 'Approve your offer online or by phone and get paid via check, PayPal, or bank transfer.',
	},
]

const steps = computed(() => (props.pageVariant === 'dental' ? DENTAL_STEPS : SCRAP_STEPS))
</script>

<style scoped>
.gc-figma-346-371 {
	width: 100%;
	background: #000;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-figma-346-371__inner {
	max-width: 1140px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 50px;
}

.gc-figma-346-371__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	align-self: stretch;
	gap: 25px;
}

.gc-figma-346-371__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 48px);
	line-height: 1.167;
	text-align: center;
	color: #fff;
}

.gc-figma-346-371__steps {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	display: grid;
	grid-template-columns: repeat(5, minmax(0, 1fr));
	gap: 10px;
	align-items: stretch;
}

.gc-figma-346-371--dental .gc-figma-346-371__steps {
	grid-template-columns: repeat(4, minmax(0, 1fr));
}

.gc-figma-346-371__step {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 40px;
	padding: 10px;
	box-sizing: border-box;
	min-height: 0;
	min-width: 0;
}

.gc-figma-346-371__num {
	margin: 0;
	width: 100%;
	max-width: 214px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 1.167;
	text-align: left;
	color: #c39e3d;
}

.gc-figma-346-371__body {
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 15px;
	flex: 1;
	width: 100%;
	max-width: 215px;
	margin: 0;
}

.gc-figma-346-371__step-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.125rem, 2vw, 30px);
	line-height: 1.133;
	color: #fff;
}

.gc-figma-346-371__step-title-one {
	display: none;
}

.gc-figma-346-371__step-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(0.9375rem, 1.5vw, 20px);
	line-height: 1;
	color: #fff;
}

@media (max-width: 1199px) {
	.gc-figma-346-371__steps {
		grid-template-columns: repeat(3, minmax(0, 1fr));
	}

	.gc-figma-346-371--dental .gc-figma-346-371__steps {
		grid-template-columns: repeat(2, minmax(0, 1fr));
	}
}

@media (max-width: 767px) {
	.gc-figma-346-371 {
		padding: 64px 0;
	}

	.gc-figma-346-371__inner {
		gap: 40px;
	}

	.gc-figma-346-371__steps {
		grid-template-columns: 1fr;
		gap: 32px;
	}

	.gc-figma-346-371__step {
		align-items: flex-start;
		gap: 24px;
		padding: 0;
	}

	.gc-figma-346-371__num {
		max-width: none;
	}

	.gc-figma-346-371__body {
		max-width: none;
	}

	.gc-figma-346-371__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-figma-346-371__step-title-lines {
		display: none;
	}

	.gc-figma-346-371__step-title-one {
		display: block;
		white-space: nowrap;
		font-size: clamp(13px, 3.6vw, 20px);
		line-height: 1.2;
	}

	/* Dental: keep line breaks / wrapping — one-line mode overlaps in 2-up layout */
	.gc-figma-346-371--dental .gc-figma-346-371__step-title-lines {
		display: block;
	}

	.gc-figma-346-371--dental .gc-figma-346-371__step-title-one {
		display: none;
	}
}
</style>
