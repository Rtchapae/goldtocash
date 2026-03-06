<template>
	<section class="section-howitworks">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h2>
						How it Works?
						<small>3 Quick Steps to Convert Your Gold Jewelry and Precious Metals into
							Cash.</small>
					</h2>
				</div>
				<div class="col-12 col-lg-6">
					<div class="row">
						<div class="col-12 col-md-4 col-lg-12" v-for="(step, idx) in steps" :key="idx">
							<span class="number">{{ step.number }}</span>
							<h4 v-html="step.title"></h4>
							<p v-html="step.text"></p>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl-5 offset-xl-1">
					<KitForm />
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'

const steps = [
	{
		number: '01',
		title: 'Request a FREE Appraisal Kit:',
		text: 'Complete a short form to get a Free Appraisal Kit. The kit includes everything you need to sell your gold.'
	},
	{
		number: '02',
		title: 'Pack &amp; Ship Your Valuables:',
		text: 'Pack your items and take it to the nearest FedEx location or request a free home pick. Free FedEx Shipping Label is provided in the kit.'
	},
	{
		number: '03',
		title: 'Accept Our Offer &amp; Get Paid:',
		text: 'Get the highest offer for your gold and receive a payment within 24 hours of accepting our offer.*'
	}
]
</script>

<style scoped>
/* Section layout */
.section-howitworks {
	background: var(--white);
	padding: 150px 0 0;
	color: var(--black);
	text-align: center;
	margin-bottom: 100px;
}
.section-howitworks h2 {
	margin: 0 0 80px;
	font-size: 48px;
	line-height: 52px;
}
.section-howitworks h2::before {
	content: "";
	display: block;
	background: var(--primary);
	height: 2px;
	width: 48px;
	margin: 0 auto 48px;
}
.section-howitworks h2 small {
    margin: 24px 0 0;
    font-size: 24px;
    line-height: 32px;
    display: block;
}
.section-howitworks h4 {
	margin: 48px 0 24px;
	font-size: 21px;
	line-height: 32px;
}
.section-howitworks span.number {
	font-size: 24px;
	line-height: 32px;
	color: var(--primary);
	margin: 48px 0 0;
	display: block;
}
@media screen and (max-width: 767px) {
	.section-howitworks {
		padding: 48px 0;
	}
	.section-howitworks h2 {
		margin: 0 0 48px;
		font-size: 36px;
		line-height: 40px;
	}
	.section-howitworks h2::before {
		margin: 0 auto 24px;
	}
	.section-howitworks h4 {
		margin: 24px 0 12px;
	}
}
</style>


