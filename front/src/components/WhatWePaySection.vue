<template>
	<section id="whatwepay" class="section-whatwepay" :class="{ 'actual-page': !embedded, 'whatwepay-home-compact': embedded }">
		<div class="container">
			<template v-if="embedded">
				<div class="row whatwepay-home-content">
					<div class="col-12 col-lg-4">
						<h2>What We Pay</h2>
						<p class="text-verdana whatwepay-home-intro">
							We offer the highest payouts for your gold, silver, platinum jewelry and bullion.
						</p>
					</div>
					<div class="col-12 col-lg-8">
						<div class="whatwepay-home-subtitle-block">
							<p class="whatwepay-home-subtitle">Our offer is based on</p>
							<div class="whatwepay-home-underline"></div>
						</div>
						<ul class="whatwepay-home-bullets list-style-none">
							<li><span>The market price of precious metals</span></li>
							<li><span>Precious metal content</span></li>
							<li><span>Precious metal weight</span></li>
							<li><span>Precious metal purity</span></li>
							<li><span>Condition of the item</span></li>
							<li><span>Total weight of precious metals</span></li>
						</ul>
					</div>
				</div>
				<div class="whatwepay-home-recent-header">
					<h3 class="whatwepay-home-recent-title">Our Recent Payouts</h3>
					<a href="/what-we-pay" class="view-more-link">
						<img src="/images/eye.svg" alt="" width="20" height="20">
						View All
					</a>
				</div>
				<div class="whatwepay-home-photos-row">
					<div
						v-for="(p, i) in homePayouts"
						:key="i"
						class="whatwepay-payout-col"
					>
						<div class="whatwepay-payout-card">
							<div class="whatwepay-payout-img-wrap">
								<img :src="p.img" :alt="p.title" class="whatwepay-payout-img">
							</div>
							<div class="whatwepay-payout-text">
								<h4 class="whatwepay-payout-title">{{ p.title }}</h4>
								<p class="whatwepay-payout-sub">{{ p.subtitle }}</p>
								<p class="whatwepay-payout-amount">{{ p.amount }}</p>
							</div>
						</div>
					</div>
				</div>
			</template>
			<template v-else>
			<div class="row">
				<div class="col-12 col-lg-6">
					<div>
						<h2>What We Pay</h2>
						<div class="text-verdana text-black">
								<p>
									<organization>Gold to Cash</organization>
									<span class="text-verdana">
										is a reputable gold buyer that pays the most for your valuables. We offer the
										highest payouts
										for your gold, silver, platinum jewelry, coins, and bullion.
									</span>
								</p>
								<p>
									<span class="text-verdana">
										Our customers get up to 95% of the market price for gold bullion and jewelry.
									</span>
								</p>
								<h3>
									Why We Pay More
								</h3>
								<p>
									<span class="text-verdana">
										We believe in complete transparency and fairness. That's why we pay up to 95% of the
										current market value for
										gold bullion and up to 90% for gold jewelry, with no hidden fees or surprise
										deductions.
										Our rates are among the highest you'll find—just compare us to any local shop or
										online
										competitor.
									</span>
								</p>
								<ul class="list-style-none padding-left-25px">
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana">
											<strong class="text-strong">Transparent Pricing:</strong>
											Our live price table is always available, so you know exactly
											what you'll be paid before you send anything in.
										</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana">
											<strong class="text-strong">No Surprises:</strong>
											Lock in your rate with a selling summary and receive a
											detailed order confirmation listing all items you plan to sell.
										</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana">
											<strong class="text-strong">Trusted Nationwide:</strong>
											Customers from Alaska
											<location>Hawaii</location>
											to New York
											<location>New York</location>
											and everywhere in between trust us with their
											precious metals and consistently return for the best deals.
										</span>
									</li>
								</ul>
								<p>
									<span class="text-verdana">
										Every item is evaluated individually—whether it's a single earring or a full
										collection, every gram is
										credited to your total payout. You can rest easy knowing you're getting the maximum
										value for your items, just as you would expect from a reputable, trustworthy
										buyer.
									</span>
								</p>
								<p>
									<span class="text-verdana">
										Ready to see how much more your gold is worth? Get started today and discover the
										Gold to Cash
										difference.
									</span>
								</p>
								<p class="text-16px text-strong line-height-20px margin-bottom-1rem margin-top-0">
									<span class="text-verdana text-black">
										Receive the
										<strong class="text-strong">Highest Offer</strong>
										for your gold jewelry and bullion - Guaranteed!
									</span>
								</p>
								<p class="text-16px text-strong line-height-20px margin-bottom-1rem margin-top-0">
									<span class="text-verdana text-black">
										<strong>Our offer is based on</strong>
									</span>
								</p>
								<ul class="list-style-none padding-left-25px">
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">The market price of precious metals</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">Precious metal content</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">Precious metal weight</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">Precious metal purity</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">Condition of the item</span>
									</li>
									<li class="position-relative margin-0-0-8px">
										<span class="text-verdana text-black text-14px">Total weight of precious metals</span>
									</li>
								</ul>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl-5 offset-xl-1">
					<KitForm />
				</div>
			</div>
			<div class="row">
				<WhatWePayDetails />
			</div>
			</template>
		</div>
	</section>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'
import WhatWePayDetails from '@/components/whatwepay/WhatWePayDetails.vue'

defineProps({
	embedded: { type: Boolean, default: false },
})

const homePayouts = [
	{ title: 'Gold Buffalo Coin', subtitle: '31.1g 24k Gold', img: '/images/payouts/gold-buffalo.jpg', amount: '$1,800.00' },
	{ title: 'Gold Necklace', subtitle: '17.5g 14k Gold', img: '/images/payouts/gold-necklace.jpg', amount: '$506.62' },
	{ title: 'Gold Pendant', subtitle: '7.2g 14k Gold', img: '/images/payouts/gold-pendant.jpg', amount: '$208.00' },
	{ title: 'Gold Ring', subtitle: '8.3g 24k Gold', img: '/images/payouts/gold-ring.jpg', amount: '$384.00' },
]
</script>

<style scoped>
.section-whatwepay.actual-page .extra-content {
	margin: 48px 0;
}

.section-whatwepay h2 {
	margin: 0 0 24px;
	font-size: 48px;
	line-height: 52px;
}

@media screen and (max-width: 767px) {
	.section-whatwepay h2 {
		font-size: 36px;
		line-height: 40px;
	}
}

.section-whatwepay h2::after {
	content: "";
	display: block;
	background: var(--white);
	height: 2px;
	width: 48px;
	margin: 4px 0 0;
}

.section-whatwepay.actual-page h2::after {
	background: var(--primary);
}

.section-whatwepay .sub-title {
	font-size: 18px;
	line-height: 24px;
}

.section-whatwepay h5 {
	border-bottom: 1px solid var(--white);
	text-transform: uppercase;
	letter-spacing: 0.08em;
	color: var(--white);
	font-size: 14px;
	line-height: 24px;
	padding: 24px 0;
	margin: 0 0 24px;
}

.section-whatwepay.actual-page h5 {
	border-bottom: 1px solid var(--black);
	color: var(--black);
}

.section-whatwepay ul {
	list-style: none;
	padding-left: 25px;
}

.section-whatwepay ul li {
	position: relative;
	margin: 0 0 8px;
}

.section-whatwepay ul li::before {
	content: "• ";
	color: var(--white);
	position: absolute;
	left: -15px;
	top: 0;
}

.section-whatwepay.actual-page ul li::before {
	color: var(--black);
}

.section-whatwepay img {
	max-width: 100%;
	margin: 1px auto 0;
}

.section-whatwepay .btn-primary {
	background: var(--primary);
	box-shadow: 0px 8px 8px rgba(219, 175, 62, 0.25);
	border-radius: 4px;
	font-size: 16px;
	line-height: 24px;
	padding: 24px;
	border: none;
	width: 100%;
}

.section-whatwepay h3 {
	font-size: 21px;
	line-height: 32px;
	margin: 24px 0;
}

/* Compact home block (Figma-style) */
.whatwepay-home-content {
	align-items: flex-start;
}
.whatwepay-home-content .col-lg-4 h2 {
	margin: 0 0 1rem;
}
.whatwepay-home-subtitle-block {
	display: flex;
	flex-direction: column;
	align-items: flex-start;
	width: 100%;
	margin-top: 1.8rem;
	margin-bottom: 1rem;
}
.whatwepay-home-subtitle {
	font-size: 18px;
	line-height: 24px;
	color: var(--white);
	margin: 0;
	font-family: Montserrat, sans-serif;
	text-transform: uppercase;
	letter-spacing: 0.02em;
}
.whatwepay-home-underline {
	height: 2px;
	background: var(--white);
	width: 100%;
	margin: 6px 0 0;
	align-self: stretch;
}
.whatwepay-home-intro {
	color: var(--black) !important;
	font-size: 16px;
	line-height: 24px;
	max-width: 320px;
}
.whatwepay-home-bullets {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 0.5rem 2rem;
	padding-left: 0;
	margin: 0;
	list-style: none;
}
.whatwepay-home-bullets li {
	position: relative;
	padding-left: 1rem;
}
.whatwepay-home-bullets li::before {
	content: "• ";
	position: absolute;
	left: 0;
	color: var(--white);
}
.whatwepay-home-bullets span {
	color: var(--black);
	font-size: 14px;
	font-family: Montserrat, sans-serif;
}
/* Figma 1800-1603: layout_XURV0K — row, space-between, alignItems center, vertical hug */
.whatwepay-home-recent-header {
	display: flex;
	flex-direction: row;
	justify-content: space-between;
	align-items: center;
	align-content: center;
	margin-bottom: 24px;
	gap: 80px;
}
/* Same as mobile: compact View All link */
.whatwepay-home-recent-header .view-more-link {
	display: inline-flex;
	align-items: center;
	gap: 4px;
	color: var(--black);
	letter-spacing: 0.1em;
	text-decoration: none;
	font-family: Montserrat, sans-serif;
	font-weight: 600;
	font-size: 14px;
}
.whatwepay-home-recent-header .view-more-link:hover {
	opacity: 0.8;
}
.whatwepay-home-recent-title {
	font-family: Montserrat, sans-serif;
	font-size: 24px;
	font-weight: 600;
	line-height: 1.33;
	color: var(--black);
	margin: 0 !important;
	padding: 0;
}
.whatwepay-home-photos-row {
	display: grid;
	grid-template-columns: repeat(4, 1fr);
	gap: 24px;
	margin-bottom: 0;
}
@media (max-width: 991px) {
	.whatwepay-home-photos-row {
		grid-template-columns: repeat(2, 1fr);
	}
}
@media (max-width: 575px) {
	.whatwepay-home-photos-row {
		grid-template-columns: 1fr;
	}
}
@media (max-width: 767px) {
	.whatwepay-home-bullets {
		grid-template-columns: 1fr;
	}
}
.whatwepay-payout-card {
	text-align: center;
	padding: 0;
	display: flex;
	flex-direction: column;
	gap: 24px;
	height: 100%;
}
.whatwepay-payout-text {
	display: flex;
	flex-direction: column;
	gap: 4px;
}
/* Figma: height 360px, objectFit cover */
.whatwepay-payout-img-wrap {
	width: 100%;
	height: 360px;
	overflow: hidden;
	border-radius: 4px;
	flex-shrink: 0;
	position: relative;
	line-height: 0;
}
.whatwepay-payout-card .whatwepay-payout-img {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	object-fit: cover;
	object-position: top center;
	display: block;
	vertical-align: top;
}
/* Figma Desktop / H3: Montserrat 600, 24px */
.whatwepay-payout-title {
	font-family: Montserrat, sans-serif;
	font-size: 24px;
	font-weight: 600;
	line-height: 1.33;
	margin: 0;
	color: var(--black);
}
/* Figma Desktop/Subhead: Montserrat 600, 14px, UPPERCASE, letterSpacing 8%, Gold */
.whatwepay-payout-sub {
	font-family: Montserrat, sans-serif;
	font-size: 14px;
	font-weight: 600;
	letter-spacing: 0.08em;
	text-transform: uppercase;
	margin: 0;
	color: var(--primary);
}
/* Figma Desktop / Excerpt: Montserrat 500, 18px */
.whatwepay-payout-amount {
	font-family: Montserrat, sans-serif;
	font-size: 18px;
	font-weight: 500;
	line-height: 1.33;
	margin: 0;
	color: var(--black);
}
.section-whatwepay.whatwepay-home-compact {
	background: linear-gradient(to bottom, #DBAF3E 0%, #DBAF3E 58%, #fff 58%);
	padding-bottom: 40px;
}
</style>
