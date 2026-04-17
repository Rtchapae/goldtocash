<template>
	<section
		id="gold-calculator-figma-38-1530"
		class="gc-calc-pay"
		aria-labelledby="gc-calc-pay-heading"
		data-figma-node="38-1530"
	>
		<div class="gc-calc-pay__inner">
			<header class="gc-calc-pay__header">
				<h2 id="gc-calc-pay-heading" class="gc-calc-pay__title">
					Gold To Cash: Accurate Estimates, Fast Payment
				</h2>
				<p class="gc-calc-pay__intro">
					While online calculators give you a great starting point, the final value of your gold is based on its
					actual weight, purity, and current market rate once tested by our team.
				</p>
				<p class="gc-calc-pay__leads">Gold To Cash provides:</p>
			</header>

			<ul class="gc-calc-pay__cards">
				<li
					v-for="(card, i) in cards"
					:key="i"
					class="gc-calc-pay__card"
					:class="card.shadow === 'gray' ? 'gc-calc-pay__card--shadow-gray' : 'gc-calc-pay__card--shadow-gold'"
				>
					<div class="gc-calc-pay__card-main">
						<div class="gc-calc-pay__card-num-layer" aria-hidden="true">
							<span class="gc-calc-pay__card-num">{{ card.num }}</span>
						</div>
						<div class="gc-calc-pay__card-copy">
							<h3 class="gc-calc-pay__card-title">{{ card.title }}</h3>
							<p class="gc-calc-pay__card-sub">{{ card.sub }}</p>
						</div>
					</div>
				</li>
			</ul>

			<p class="gc-calc-pay__footnote">
				We’re known for honest pricing, professional service, and industry-leading payouts with thousands of satisfied
				customers nationwide.
			</p>
		</div>
	</section>
</template>

<script setup>
const cards = [
	{
		num: '1',
		title: 'Free mail-in\nappraisal kits',
		sub: 'with insured shipping',
		shadow: 'gold'
	},
	{
		num: '2',
		title: 'Same-day\nevaluations',
		sub: 'upon arrival',
		shadow: 'gray'
	},
	{
		num: '3',
		title: 'Instant\npayment options',
		sub: 'via check, bank transfer, or PayPal',
		shadow: 'gold'
	}
]
</script>

<style scoped>
.gc-calc-pay {
	width: 100%;
	background: #fff;
	padding: 100px 0;
	box-sizing: border-box;
}

.gc-calc-pay__inner {
	max-width: 1280px;
	margin: 0 auto;
	padding: 0 24px;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
	box-sizing: border-box;
}

.gc-calc-pay__header {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 20px;
	padding: 30px 0 0;
	width: 100%;
	max-width: 1226px;
	text-align: center;
}

.gc-calc-pay__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 4vw, 3rem);
	line-height: 1.17;
	color: #dbaf3e;
}

.gc-calc-pay__intro,
.gc-calc-pay__leads {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #000;
	max-width: 1226px;
}

.gc-calc-pay__cards {
	list-style: none;
	margin: 0;
	padding: 0;
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
	align-items: stretch;
	gap: 30px;
	width: 100%;
	box-sizing: border-box;
}

/* Background + radius on ::before only — parent border-radius would clip large digits (Chrome/Safari). */
.gc-calc-pay__card {
	position: relative;
	width: 100%;
	max-width: 390px;
	min-height: 400px;
	box-sizing: border-box;
	overflow: visible;
	background: transparent;
	border: none;
	border-radius: 0;
}

.gc-calc-pay__card::before {
	content: '';
	position: absolute;
	inset: 0;
	z-index: 0;
	border-radius: 20px;
	background: #000;
	border: 2px solid #000;
	box-sizing: border-box;
	pointer-events: none;
}

.gc-calc-pay__card--shadow-gold::before {
	box-shadow: 12px 12px 0 0 #c39e3d;
}

.gc-calc-pay__card--shadow-gray::before {
	box-shadow: 12px 12px 0 0 #cecece;
}

.gc-calc-pay__card-main {
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: stretch;
	justify-content: center;
	gap: 12px;
	min-height: 400px;
	padding: 28px 22px;
	box-sizing: border-box;
	overflow: hidden;
}

.gc-calc-pay__card-num-layer {
	position: absolute;
	inset: 0;
	z-index: 0;
	display: flex;
	align-items: center;
	justify-content: flex-end;
	padding-right: 4%;
	pointer-events: none;
	overflow: hidden;
}

.gc-calc-pay__card-copy {
	min-width: 0;
	align-self: flex-start;
	position: relative;
	z-index: 1;
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	gap: 10px;
	max-width: 100%;
	text-align: left;
}

.gc-calc-pay__card-title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.25rem, 2.5vw, 1.875rem);
	line-height: 1.13;
	color: #fff;
	white-space: pre-line;
}

.gc-calc-pay__card-sub {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	color: #fff;
}

.gc-calc-pay__card-num {
	flex: 0 1 auto;
	min-width: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 800;
	font-size: clamp(11rem, 42vw, 22rem);
	line-height: 1;
	letter-spacing: 0;
	text-align: center;
	display: block;
	transform: translateX(10px) scale(1.08);
	opacity: 0.24;
	background-image: linear-gradient(175deg, rgba(250, 250, 250, 0.55) 0%, rgba(160, 160, 160, 0.48) 100%);
	-webkit-background-clip: text;
	background-clip: text;
	color: rgba(255, 255, 255, 0.18);
	user-select: none;
	pointer-events: none;
}

@supports ((-webkit-background-clip: text) or (background-clip: text)) {
	.gc-calc-pay__card-num {
		color: transparent;
	}
}

.gc-calc-pay__footnote {
	margin: 0;
	max-width: 1226px;
	width: 100%;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: clamp(1rem, 1.5vw, 1.25rem);
	line-height: 1.2;
	text-align: center;
	color: #000;
}

@media (max-width: 991.98px) {
	.gc-calc-pay {
		padding: 60px 0 80px;
	}

	.gc-calc-pay__card {
		max-width: 100%;
	}

	.gc-calc-pay__card-main {
		min-height: 360px;
	}
}

@media (max-width: 767.98px) {
	.gc-calc-pay__title {
		font-size: 30px;
		line-height: 1.13;
	}

	.gc-calc-pay__intro,
	.gc-calc-pay__leads {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-calc-pay__footnote {
		font-size: 24px;
		line-height: 1.25;
	}

	.gc-calc-pay__card {
		display: flex;
		flex-direction: column;
		min-height: 0;
		aspect-ratio: 1;
		width: 100%;
		max-width: min(100%, 320px);
		margin-left: auto;
		margin-right: auto;
	}

	.gc-calc-pay__card-main {
		flex: 1;
		min-height: 0;
		height: 100%;
		padding: 14px 12px 14px 10px;
		gap: 10px;
		align-items: stretch;
	}

	.gc-calc-pay__card-num-layer {
		padding-right: 2%;
	}

	.gc-calc-pay__card-title {
		font-size: 30px;
		line-height: 1.15;
	}

	.gc-calc-pay__card-sub {
		font-size: 20px;
		line-height: 1.25;
	}

	.gc-calc-pay__card-num {
		font-size: clamp(9rem, 52vw, 16rem);
		opacity: 0.2;
		transform: translateX(12px) scale(1.12);
	}
}
</style>
