<template>
	<section class="wwb-process" aria-labelledby="wwb-process-title">
		<div class="wwb-process__inner">
			<header class="wwb-process__header">
				<h2 id="wwb-process-title" class="wwb-process__title">Our Process Is Simple!</h2>
			</header>

			<div class="wwb-process__grid">
				<ol class="wwb-process__steps" role="list">
					<li v-for="step in steps" :key="step.num" class="wwb-process__step">
						<p class="wwb-process__step-num" aria-hidden="true">{{ step.num }}</p>
						<div class="wwb-process__step-body">
							<h3 class="wwb-process__step-title">{{ step.title }}</h3>
							<p class="wwb-process__step-text">{{ step.body }}</p>
						</div>
					</li>
				</ol>

				<div class="wwb-process__form-wrap">
					<div class="wwb-process__form-card">
						<KitForm />
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
		title: 'Request Your Free Appraisal Kit',
		body:
			'Fill out the quick form to claim your free kit. We’ll send you everything you need to safely mail in your gold or jewelry for evaluation. The Gold-to-Cash team takes it from there…',
	},
	{
		num: '02',
		title: 'Next, We Test and Value Your Items',
		body:
			'As soon as we receive your gold, our team tests each item carefully to determine the purity and weight. Our offer is based on the current gold price — the condition of the jewelry doesn’t matter, and there are no hidden fees.',
	},
	{
		num: '03',
		title: 'And Finally, Accept the Offer & Get Paid',
		body:
			'We pride ourselves on our quick turn-around time. You’ll have an offer within 24 Hours - guaranteed! There’s no obligation to accept, but if you do, we’ll send your payment the same day.',
	},
]
</script>

<style scoped>
/* Figma 675:1904 — white section */
.wwb-process {
	width: 100%;
	background: #fff;
	padding: 60px 0 100px;
	box-sizing: border-box;
}

.wwb-process__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
}

.wwb-process__header {
	text-align: center;
	margin-bottom: 48px;
}

.wwb-process__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.5vw, 40px);
	line-height: 1.2;
	color: #000;
}

.wwb-process__grid {
	display: grid;
	grid-template-columns: minmax(0, 1fr) minmax(260px, 460px);
	gap: 48px;
	align-items: start;
}

.wwb-process__steps {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 36px;
	text-align: left;
}

.wwb-process__step {
	display: flex;
	flex-direction: column;
	gap: 8px;
}

.wwb-process__step-num {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 26px;
	line-height: 1.3;
	color: #c39e3d;
}

.wwb-process__step-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 26px;
	line-height: 1.35;
	color: #000;
}

.wwb-process__step-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 16px;
	line-height: 1.5;
	color: #000;
}

.wwb-process__form-wrap {
	width: 100%;
	max-width: 460px;
	justify-self: end;
}

.wwb-process__form-card {
	background: #fff;
	border-radius: 12px;
	box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
	padding: 8px 8px 16px;
	box-sizing: border-box;
	border: 1px solid #eee;
	width: 100%;
	max-width: 460px;
}

.wwb-process__form-card :deep(.kit-form-title) {
	margin: 0 0 24px;
	padding: 8px 8px 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: clamp(1.75rem, 3vw, 50px);
	line-height: 1.15;
	text-align: center;
	color: #000;
}

.wwb-process__form-card :deep(.kit-form-title)::after {
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
	.wwb-process {
		padding: 40px 0 64px;
	}

	.wwb-process__grid {
		grid-template-columns: 1fr;
		gap: 40px;
	}

	.wwb-process__form-wrap,
	.wwb-process__form-card {
		max-width: 460px;
		justify-self: center;
		margin: 0 auto;
	}

	.wwb-process__step-num,
	.wwb-process__step-title,
	.wwb-process__step-text {
		text-align: center;
	}
}
</style>
