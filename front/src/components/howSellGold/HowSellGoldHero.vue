<template>
	<section class="hsg-hero" aria-labelledby="hsg-hero-title">
		<div class="hsg-hero__inner">
			<header class="hsg-hero__header">
				<h1 id="hsg-hero-title" class="hsg-hero__title">How To Sell Gold</h1>
				<p class="hsg-hero__subtitle">
					3 Easy Steps to Get Cash for Gold and Sell Jewelry for Cash!
				</p>
			</header>

			<div class="hsg-hero__grid">
				<ol class="hsg-hero__steps" role="list">
					<li v-for="step in steps" :key="step.num" class="hsg-hero__step">
						<p class="hsg-hero__step-num" aria-hidden="true">{{ step.num }}</p>
						<div class="hsg-hero__step-body">
							<h2 class="hsg-hero__step-title">{{ step.title }}</h2>
							<p class="hsg-hero__step-text">{{ step.body }}</p>
						</div>
					</li>
				</ol>

				<div class="hsg-hero__form-wrap">
					<div class="hsg-hero__form-card">
						<KitForm />
						<p class="hsg-hero__bonus" aria-hidden="true">Get 10% Bonus</p>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'

const steps = [
	{
		num: '01',
		title: 'Claim Your Free Appraisal Kit',
		body:
			'Begin the process when you receive your appraisal kit. This free kit includes everything you will need to sell your gold items. Simply fill out the free appraisal kit.',
	},
	{
		num: '02',
		title: 'We Evaluate & Price Your Gold',
		body:
			'Once your items arrive at our office, an expert from our team will be assigned to process your shipment. Our experts begin by utilizing new technology and other testing to establish the purity of your gold. Even though we closely examine your items’ every detail - we only factor weight and purity into your offer based on the daily gold price.',
	},
	{
		num: '03',
		title: 'Get Paid by Accepting Our Cash Offer',
		body:
			'Once we process your item or items, you receive an offer within 24 hours. Remember, you are in no way obligated to accept our cash offer. However, we are confident you will be happy with our offer just like thousands of other customers. Once the offer is accepted, your cash payout will be issued the same day.',
	},
]
</script>

<style scoped>
.hsg-hero {
	width: 100%;
	background: #f5f1e7;
	/* Clear fixed navbar (+ top banner on desktop) */
	padding: 150px 0 100px;
	box-sizing: border-box;
}

.hsg-hero__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.hsg-hero__header {
	text-align: center;
	margin-bottom: 48px;
}

.hsg-hero__title {
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 1.17;
	color: #000;
}

.hsg-hero__subtitle {
	margin: 0 auto;
	max-width: 720px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 2vw, 20px);
	line-height: 1.4;
	color: #000;
}

.hsg-hero__grid {
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(260px, 460px);
	gap: 48px;
	align-items: start;
}

.hsg-hero__steps {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 36px;
	text-align: left;
}

.hsg-hero__step {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.hsg-hero__step-num {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 26px;
	line-height: 1.3;
	color: #c39e3d;
	text-align: center;
}

.hsg-hero__step-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 21px;
	line-height: 1.35;
	color: #000;
	text-align: center;
}

.hsg-hero__step-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 16px;
	line-height: 1.5;
	color: #000;
	text-align: center;
}

.hsg-hero__form-wrap {
	position: relative;
	max-width: 460px;
	width: 100%;
	margin-left: auto;
}

.hsg-hero__form-card {
	position: relative;
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
	padding: 8px 8px 16px;
	box-sizing: border-box;
	max-width: 460px;
	width: 100%;
}

/* Figma 678:2692 — “Request Free Kit” + “Get 10% Bonus” as stacked centered headings */
.hsg-hero__bonus {
	display: none;
}

.hsg-hero__form-card :deep(.kit-form-title) {
	margin: 0 0 24px;
	padding: 8px 8px 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.75rem, 3vw, 50px);
	line-height: 1.15;
	text-align: center;
	color: #000;
}

.hsg-hero__form-card :deep(.kit-form-title)::after {
	content: 'Get 10% Bonus';
	display: block;
	margin-top: 4px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.25rem, 2.4vw, 32px);
	line-height: 1.2;
	color: #c39e3d;
}

@media (max-width: 991.98px) {
	.hsg-hero {
		padding: 140px 0 64px;
	}

	.hsg-hero__grid {
		grid-template-columns: 1fr;
		gap: 40px;
	}

	.hsg-hero__header {
		margin-bottom: 32px;
	}

	.hsg-hero__steps {
		gap: 28px;
	}

	.hsg-hero__form-wrap,
	.hsg-hero__form-card {
		max-width: 460px;
		margin-left: auto;
		margin-right: auto;
	}
}
</style>
