<template>
	<section class="section-faq">
		<div class="container">
			<h1>FAQ</h1>
			<div id="accordion">
				<div class="card" v-for="(item, idx) in faqItems" :key="idx">
					<div class="card-header">
						<button class="card-link" :aria-expanded="openIndex === idx" @click="toggle(idx)">
							{{ item.question }}
						</button>
					</div>
					<div class="collapse" :class="{ show: openIndex === idx }">
						<div class="card-body">
							<div v-html="item.answer"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref } from 'vue'

const openIndex = ref(0)
function toggle(idx) {
	openIndex.value = openIndex.value === idx ? -1 : idx
}

const faqItems = [
	{
		question: "What's the process to sell my gold jewelry?",
		answer: `
<p>Selling at Gold to Cash is very easy and secure. Simply complete the Free Appraisal Request and submit the form to start the process. You will then be asked to create an account to track the status of your order.</p>
<p>You will have three options to proceed: Print Welcome Letter &amp; Instruction, Email and print later or Request a Free Kit mailed directly to your home. For the fastest and smoothest transaction please select 1st or 2nd option.</p>
<p>Pack your valuables and enclose the completed and signed Information Card that is included with the Welcome Letter. Once that is done, take it to the nearest FedEx location and ship it to us using a prepaid FedEx shipping label that is also included with the Welcome Letter.</p>
<p>When we receive your items we will scan it in the system and update it in your account. Our experienced and trained personnel will complete tests and appraise your items the same day we receive your package. After the appraisal we will make an offer for your gold jewelry or precious metals. To accept the offer simply respond in your account or contact us by calling <a href="tel:5642377332">(564) 237-7332</a> or email us at <a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a> After you have accepted our offer we will initiate a payment within 24 hours.</p>
`
	},
	{
		question: "Does it cost anything to use this service?",
		answer: `
<p>Selling gold jewelry and precious metals to Gold to Cash is completely FREE. We provide everything you need to sell your items and get paid. Even if you’re not completely satisfied with our offer you will receive your items free of charge.</p>
`
	},
	{
		question: "How long does it take?",
		answer: `
<p>At Gold to Cash you can get paid within 24 hours from the minute you click Request Free Appraisal. We made the entire process very fast and convenient so you can receive money for your jewelry or precious metals the next business day.</p>
<p>*Please note that is possible only if you select Print Welcome Letter or Email to Print later.</p>
<p>*You can get your Free Kit in the mail, however the entire process can stretch up to 5 business days.</p>
`
	},
	{
		question: "How can I be sure my valuables are safe and I won't be scammed?",
		answer: `
<p>Gold to Cash is 100% Safe and Trustworthy online gold buyer. Licensed under Washington State Chapter 19.60 RCW Secondhand Precious Metals Dealer. Many customers trusted to sell their gold jewelry and precious metals to Gold to Cash and never regretted.</p>
<p>Your valuables are safe at every stage of transaction with GTC. All valuables are stored in a world class vault and secured by armed staff. Please call us if you have any questions or concerns.</p>
<p>Read Verified customer reviews.</p>
`
	},
	{
		question: "Do you have a storefront location for walk-ins?",
		answer: `
<p>No, we do not. For the safety of our personnel and your valuables we don’t have a storefront location and do not operate on a walk in basis. However, we do have a designated facility where our professional and experienced personnel performs tests and appraisals.</p>
`
	},
	{
		question: "What if I don’t want to accept your offer?",
		answer: `
<p>Our personnel are very well trained and experienced, therefore we hope you will be happy with our offer and satisfied with every step of the transaction. However, if you are not satisfied with our offer you have 72 hours to decline it. We will safely return your items back to you at no cost.</p>
`
	},
	{
		question: "What is a Price Match Guarantee?",
		answer: `
<p>Gold to Cash is striving to provide the best service in the industry and we want our customers to be happy with our offer. If you have a higher offer from an online competitor, we can match their offer.* Terms and Conditions apply.</p>
`
	},
	{
		question: "What do you buy?",
		answer: `
<p>Gold to Cash buys everything that contains precious metals. Send us your gold, silver and/or platinum for the highest offer.</p>
<p>Jewelry:</p>
<ul>
	<li>Rings, necklaces, earrings, bracelets, pendants.</li>
	<li>All purities and sizes.</li>
	<li>Gold, silver, platinum.</li>
	<li>New, used, broken and damaged.</li>
	<li>Designer jewelry and luxury watches.</li>
</ul>
<p>Coins:</p>
<ul>
	<li>Any gold, silver and platinum coins.</li>
	<li>Collectible coins.</li>
	<li>Any country and denomination.</li>
</ul>
<p>Bullion:</p>
<ul>
	<li>Any gold, silver and platinum bullion.</li>
	<li>Bars of any weight.</li>
	<li>Rounds of any weight.</li>
</ul>
<p>Precious Metals:</p>
<ul>
	<li>Gold dentures</li>
	<li>Gold granules</li>
	<li>Gold nuggets</li>
	<li>Sterling silver flatware</li>
</ul>
`
	},
	{
		question: "How do I get paid?",
		answer: `
<p>We want to make every step of the process convenient and fast. That is why we offer four payment options.</p>
<ol>
	<li>Wire Transfer / ACH: Fast and Free! Direct deposit into your bank account within 1-3 business days.</li>
	<li>Company Check: Mailed to the address we have on file within 24 hours. Default payment method if you leave payment option field blank in the Information Card or if there is an issue with using another payment option.</li>
	<li>Cash App: Fast and Free! Very convenient option if you want to get paid faster.</li>
	<li>PayPal: Fast and Free! Very convenient option if you want to get paid faster.</li>
</ol>
`
	},
	{
		question: "Can I change my preferred method of payment?",
		answer: `
<p>Yes, you can change your preferred method of payment at any time before we initiate a payment. The fastest and most convenient way to change your payment method is to log into your Account and do the change in Personal Information tab. Feel free to contact us via phone or email to request a change.</p>
`
	},
	{
		question: "Why do I need an account?",
		answer: `
<p>You need an account for the following reasons:</p>
<ul>
	<li>Check the status of your transaction with Gold to Cash.</li>
	<li>Track your package after you dropped it at the staffed FedEx location.</li>
	<li>Review your transaction and respond to our offer for your valuables.</li>
	<li>Update your personal information and preferred method of payment.</li>
	<li>Update and complete the Information Card.</li>
	<li>Request another appraisal.</li>
	<li>Collect your bonus for the referral program.</li>
</ul>
`
	},
	{
		question: "How do I access My Account?",
		answer: `
<p>When you request a Free Appraisal Kit you will be asked to create a password for your account. Your login is the email address you provided for your Free Appraisal Kit. If you forgot your password you can reset it at goldtocash.com or by contacting us via phone or email.</p>
`
	},
	{
		question: "Do you provide insurance while my items are in transit?",
		answer: `
<p>Yes, the prepaid FedEx shipping label that comes in the Appraisal Kit is automatically insured up to $1,000 in the event of loss or damage. If your valuables exceed that value, you can request additional coverage by calling us.</p>
`
	}
]
</script>

<style scoped>
.section-faq {
	padding: 245px 0;
}

.section-faq h1 {
	font-size: 72px;
	line-height: 80px;
	margin: 0 0 80px;
}

.section-faq .card {
	margin: 0 0 24px;
}

.section-faq .card-header {
	padding: 0;
	border: none;
	background: transparent;
}

.section-faq .card-link {
	font-size: 24px;
	line-height: 32px;
	color: var(--black);
	padding: 32px;
	display: block;
	font-weight: 600;
	background: none !important;
}

.section-faq .card-body {
	padding: 0 32px 32px;
}
.section-faq .card p a {
	padding: 0;
	color: var(--black);
	display: inline-block;
	text-decoration: underline;
}

.section-faq .card ol,
.section-faq .card ul {
	padding-left: 30px;
	margin: 24px 0;
}

.section-faq .card ol li,
.section-faq .card ul li {
	margin: 0 0 12px;
}

@media screen and (max-width: 767px) {
	.section-faq { padding: 48px 0; }
	.section-faq h1 { margin: 0 0 48px; font-size: 44px; line-height: 48px; }
	.section-faq .card-link { font-size: 18px; line-height: 24px; padding: 24px; }
	.section-faq .card-body { padding: 0 24px 24px; }
}
</style>