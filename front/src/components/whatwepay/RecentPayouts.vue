<template>
	<section class="section-recent-payouts">
		<div class="container">
			<h2 id="recentPayouts">Our Recent Payouts</h2>
			<div class="row">
				<div class="col-12 col-sm-6 col-md-4 col-lg-3 payout-column" v-for="p in payouts" :key="p.title + p.subtitle">
					<h4>{{ p.title }}</h4>
					<h5 v-html="p.subtitle"></h5>
					<img :src="p.img" alt="Icon">
					<h5>{{ p.amount }}</h5>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
const payouts = [
	{ title: 'Gold Buffalo', subtitle: '31.1g 24k Gold', img: '/images/payouts/gold-buffalo.jpg', amount: '$1,800.00' },
	{ title: 'Gold Ring', subtitle: '10.7g 14k Gold', img: '/images/payouts/gold-ring.jpg', amount: '$290.49' },
	{ title: 'Silver Eagles', subtitle: '20.9999 Silver Coin', img: '/images/payouts/silver-eagles.jpg', amount: '$570.00' },
	{ title: 'Gold Necklace', subtitle: '17.5g 14k Gold', img: '/images/payouts/gold-necklace.jpg', amount: '$506.62' },
	{ title: 'Rolex Daytona', subtitle: '156.2g 18k Gold Watch', img: '/images/payouts/rolex-daytona.jpg', amount: '$14,000.00' },
	{ title: 'Gold Pendant', subtitle: '7.2g 14k Gold', img: '/images/payouts/gold-pendant.jpg', amount: '$208.00' },
	{ title: 'Gold Rings', subtitle: '7.8g 14k Gold', img: '/images/payouts/gold-rings.jpg', amount: '$250.00' },
	{ title: 'Gold Ring', subtitle: '8.6g 18k Gold', img: '/images/payouts/gold-ring-2.jpg', amount: '$320.05' },
	{ title: 'Gold Bar Pendant', subtitle: '1g 24k Gold', img: '/images/payouts/gold-bar-pendant.jpg', amount: '$50.00' },
	{ title: 'Gold Ring', subtitle: '8.3g 24k Gold', img: '/images/payouts/gold-ring-3.jpg', amount: '$384.00' },
	{ title: 'Gold Ring', subtitle: '5.1g 14k Gold', img: '/images/payouts/gold-ring-4.jpg', amount: '$138.52' },
	{ title: 'Gold Necklace', subtitle: '14.1g 14k Gold', img: '/images/payouts/gold-necklace-2.jpg', amount: '$380.25' }
]
</script>

<style scoped>
.section-recent-payouts {
	text-align: center;
	padding: 0 0 80px;
}
.section-recent-payouts .payout-column {
	padding-top: 24px;
	padding-bottom: 24px;
	margin-bottom: 24px;
	border-top: 2px solid #e4e000;
}
.section-recent-payouts h2 {
	margin: 0 0 48px;
	font-size: 36px;
	line-height: 44px;
	text-align: left;
}
.section-recent-payouts h4 {
	margin: 0 0 12px;
	font-size: 18px;
	line-height: 24px;
}
.section-recent-payouts h5 {
	margin: 0;
	font-size: 14px;
	line-height: 20px;
	color: #111111;
}
.section-recent-payouts img {
	position: relative;
	max-width: 100%;
	margin: 12px 0;
	transition: all 200ms;
}
@media only screen and (min-width: 768px) {
	.section-recent-payouts img:hover {
		transform: scale(1.25);
		z-index: 20;
	}
}
</style>


