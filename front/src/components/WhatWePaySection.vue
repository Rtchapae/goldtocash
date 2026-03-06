<template>
	<section id="whatwepay" class="section-whatwepay actual-page">
		<div class="container">
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
										<strong class="text-strong">
											Transparent Pricing:
										</strong>
										Our live price table is always available, so you know exactly
										what you'll be paid before you send anything in.
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana">
										<strong class="text-strong">
											No Surprises:
										</strong>
										Lock in your rate with a selling summary and receive a
										detailed order confirmation listing all items you plan to sell.
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana">
										<strong class="text-strong">
											Trusted Nationwide:
										</strong>
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
									<strong>
										Our offer is based on
									</strong>
								</span>
							</p>
							<ul class="list-style-none padding-left-25px">
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										The market price of precious metals
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										Precious metal content
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										Precious metal weight
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										Precious metal purity
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										Condition of the item
									</span>
								</li>
								<li class="position-relative margin-0-0-8px">
									<span class="text-verdana text-black text-14px">
										Total weight of precious metals
									</span>
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
		</div>
	</section>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'
import WhatWePayDetails from '@/components/whatwepay/WhatWePayDetails.vue'
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
	margin: 24px 0 0;
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
	margin: 80px auto 0;
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
</style>
