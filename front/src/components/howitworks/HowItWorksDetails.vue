<template>
	<section class="sections-howitworks-content">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h2>Give it a try.</h2>
					<p>{{ intro }}</p>
					<ul>
						<li v-for="(item, idx) in bulletPoints" :key="idx" v-html="item"></li>
					</ul>
					<p>{{ outro }}</p>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
const intro = 'Selling gold online should be quick and easy, however you might feel hesitant not knowing what to expect. We want to be transparent and help you gain confidence to sell gold to Gold to Cash.'
const bulletPoints = [
	'As soon as you complete a short form on our website we will ask you to create an account to track the status of your appraisal. Once you have an account you will need to select one of the options to proceed. (1) Print Welcome Letter &amp; Instructions (2) Email to Print Later or (3) Request a Free Appraisal Kit mailed directly to your home. For the fastest and smoothest transaction please select 1st or 2nd option.',
	'Package your valuables discreetly and enclose the completed and signed Information Card that is included with the Welcome Letter. Take your package to the nearest FedEx location and ship it to our appraising facility using a prepaid FedEx shipping label that is also included with the Welcome Letter. Make sure to get a receipt that contains a tracking number and estimated arrival date.',
	'When we receive your items we will scan them in the system and update the status in your account. Our experienced and trained personnel will complete tests and appraise your items the same day we receive your package. After the appraisal we will make an offer for your gold jewelry or precious metals and provide a detailed Offer Report. We offer the highest payouts and we want you to see what our offer is based on. To accept our offer simply respond in your account or contact us. After you have accepted our offer we will initiate a payment within 24 hours. If you’re not satisfied with the offer we will send your items back at no cost.'
]
const outro = 'The whole process is completely free and has no obligation. Give us a try!'
</script>

<style scoped>
/* Section layout */
.sections-howitworks-content {
	padding: 150px 0;
}
.sections-howitworks-content h2 {
	margin: 0 0 48px;
	font-size: 48px;
	line-height: 52px;
	text-align: center;
}
.sections-howitworks-content ul {
	margin: 24px 0 48px;
}
.sections-howitworks-content ul li {
	margin: 0 0 12px;
}
@media screen and (max-width: 767px) {
	.sections-howitworks-content {
		padding: 48px 0;
	}
}
</style>


