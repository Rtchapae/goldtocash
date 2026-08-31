<template>
	<section class="hsg-trust" aria-labelledby="hsg-trust-title">
		<div class="hsg-trust__inner">
			<header class="hsg-trust__header">
				<h2 id="hsg-trust-title" class="hsg-trust__title">
					Why Gold to Cash is Trusted by Thousands of Americans
				</h2>
				<p class="hsg-trust__intro">
					Every transaction with Gold to Cash is built on transparency, accuracy, and security. Here’s what you can
					expect:
				</p>
			</header>

			<ul class="hsg-trust__grid" role="list">
				<li v-for="(item, i) in items" :key="i" class="hsg-trust__item">
					<img
						class="hsg-trust__icon"
						:src="item.iconSrc"
						alt=""
						width="40"
						height="40"
						loading="lazy"
						decoding="async"
					/>
					<p class="hsg-trust__text">{{ item.label }}</p>
				</li>
			</ul>

			<p class="hsg-trust__footer">
				Your gold deserves a fair and honest evaluation - and that’s exactly what we provide.
			</p>
		</div>
	</section>
</template>

<script setup>
const items = [
	{
		label: 'Our Experts provide professional testing to accurately determine your gold’s purity',
		iconSrc: '/images/how-sell-gold/icon-testing.svg',
	},
	{
		label: 'Our team provides clear documentation and recorded appraisals for full transparency and pricing',
		iconSrc: '/images/how-sell-gold/icon-docs.svg',
	},
	{
		label:
			'Fully insured shipping up to $5,000 for both ways for your peace of mind and protection. *$100k insurance available upon request.',
		iconSrc: '/images/how-sell-gold/icon-shipping.svg',
	},
	{
		label: 'With Gold to Cash - there are No hidden fees, deductions, or surprises',
		iconSrc: '/images/how-sell-gold/icon-no-fees.svg',
	},
	{
		label: 'Our quick processing allows us to deliver our customers their quotes within 24 hours',
		iconSrc: '/images/how-sell-gold/icon-24h.svg',
	},
	{
		label: 'No flat rates! We provide some of the highest market payouts based on current gold spot pricing.',
		iconSrc: '/images/how-sell-gold/icon-payouts.svg',
	},
	{
		label: 'Our Process is simple, hassle free with no long negotiations or low-ball offers',
		iconSrc: '/images/how-sell-gold/icon-process.svg',
	},
	{
		label:
			'Our Gold Standard pricing comes from our online transactions allowing us to keep expenses and overhead to a minimum.',
		iconSrc: '/images/how-sell-gold/icon-overhead.svg',
	},
]
</script>

<style scoped>
.hsg-trust {
	width: 100%;
	background: #000;
	padding: 100px 0;
	box-sizing: border-box;
	color: #fff;
}

.hsg-trust__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 48px;
}

.hsg-trust__header {
	text-align: center;
	max-width: 900px;
}

.hsg-trust__title {
	margin: 0 0 16px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.5vw, 40px);
	line-height: 1.2;
	color: #fff;
}

.hsg-trust__intro {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: 18px;
	line-height: 1.45;
	color: #c39e3d;
}

.hsg-trust__grid {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	max-width: 1057px;
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 28px 48px;
}

.hsg-trust__item {
	display: flex;
	align-items: flex-start;
	gap: 16px;
}

.hsg-trust__icon {
	flex: 0 0 40px;
	width: 40px;
	height: 40px;
	object-fit: contain;
}

.hsg-trust__text {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 16px;
	line-height: 1.45;
	color: #fff;
}

.hsg-trust__footer {
	margin: 0;
	text-align: center;
	max-width: 720px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-size: 16px;
	line-height: 1.45;
	color: #fff;
}

@media (max-width: 767.98px) {
	.hsg-trust {
		padding: 64px 0;
	}

	.hsg-trust__grid {
		grid-template-columns: 1fr;
		gap: 24px;
	}
}
</style>
