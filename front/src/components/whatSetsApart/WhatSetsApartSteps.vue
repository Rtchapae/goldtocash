<template>
	<section class="wsa-steps" aria-labelledby="wsa-steps-title">
		<div class="wsa-steps__inner">
			<header class="wsa-steps__header">
				<h2 id="wsa-steps-title" class="wsa-steps__title">Simple Steps to Get Paid</h2>
				<p class="wsa-steps__intro">
					Turning your gold into cash is quick and easy. Our process is designed to be straightforward, secure, and
					hassle-free from start to finish.
				</p>
			</header>

			<ol class="wsa-steps__list" role="list">
				<li v-for="step in steps" :key="step.num" class="wsa-steps__item">
					<p class="wsa-steps__num" aria-hidden="true">{{ step.num }}</p>
					<div class="wsa-steps__body">
						<h3 class="wsa-steps__item-title">{{ step.title }}</h3>
						<p class="wsa-steps__item-text">{{ step.body }}</p>
					</div>
				</li>
			</ol>
		</div>
	</section>
</template>

<script setup>
/** Figma 641:2650 — Simple Steps to Get Paid (5 desktop columns) */
const steps = [
	{
		num: '01',
		title: 'Claim your Free Appraisal Kit',
		body:
			'Start by completing the quick form on this page. We’ll send you a secure, prepaid envelope along with everything you need to safely mail in your gold. Your shipment is fully insured for up to $5,000, so you can send your items with confidence.',
	},
	{
		num: '02',
		title: 'Mail in Your Gold',
		body:
			'Place your gold items in the envelope and seal it securely. Then simply drop it off at any USPS location near you. Shipping is fast, convenient, and fully insured every step of the way.',
	},
	{
		num: '03',
		title: 'Expert Gold Evaluation',
		body:
			'Once your package arrives, our experienced appraisers carefully weigh and test each item. Using precise, industry-grade equipment, we determine the purity and value of your gold to ensure an accurate assessment.',
	},
	{
		num: '04',
		title: 'Get your Cash Offer',
		body:
			'Within 24 hours, you’ll receive a competitive offer based on the current gold market price. Our pricing is transparent, with no hidden fees or surprise deductions.',
	},
	{
		num: '05',
		title: 'Accept & Get Paid Fast',
		body:
			'If you’re happy with the offer, simply approve it and we’ll send your payment right away. You can choose to receive your money by check, PayPal, or bank transfer, whichever works best for you',
	},
]
</script>

<style scoped>
/* Figma 641:2650 — white section, 5 equal columns on desktop */
.wsa-steps {
	width: 100%;
	background: #fff;
	padding: 120px 0 100px;
	box-sizing: border-box;
}

.wsa-steps__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	gap: 50px;
}

.wsa-steps__header {
	width: 100%;
	text-align: left;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 20px;
	padding: 30px 0;
	box-sizing: border-box;
}

.wsa-steps__title {
	margin: 0;
	max-width: 1227px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #000;
	text-align: left;
}

.wsa-steps__intro {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1.125rem, 2vw, 24px);
	line-height: 32px;
	color: #c39e3d;
	text-align: left;
}

.wsa-steps__list {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	display: grid;
	grid-template-columns: repeat(5, minmax(0, 1fr));
	gap: 15px;
	align-items: start;
}

.wsa-steps__item {
	margin: 0;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 40px;
	min-width: 0;
}

.wsa-steps__num {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 32px;
	line-height: 32px;
	color: #c39e3d;
}

.wsa-steps__body {
	display: flex;
	flex-direction: column;
	gap: 15px;
	min-width: 0;
}

.wsa-steps__item-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 30px;
	line-height: 34px;
	color: #000;
}

.wsa-steps__item-text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #000;
}

@media (max-width: 1199.98px) {
	.wsa-steps__item-title {
		font-size: 24px;
		line-height: 1.25;
	}

	.wsa-steps__item-text {
		font-size: 16px;
		line-height: 1.45;
	}
}

@media (max-width: 991.98px) {
	.wsa-steps {
		padding: 64px 0;
	}

	.wsa-steps__inner {
		gap: 40px;
	}

	.wsa-steps__header {
		padding: 0;
		gap: 16px;
		align-items: center;
		text-align: center;
	}

	.wsa-steps__title,
	.wsa-steps__intro {
		text-align: center;
		line-height: 1.2;
	}

	.wsa-steps__intro {
		line-height: 1.4;
	}

	.wsa-steps__list {
		grid-template-columns: 1fr;
		gap: 32px;
	}

	.wsa-steps__item {
		gap: 12px;
	}

	.wsa-steps__item-title {
		font-size: 22px;
	}
}
</style>
