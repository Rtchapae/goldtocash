<template>
	<section class="section-goldcash">
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-10 offset-lg-1">
					<h2>The
						<span>Gold to Cash</span> Difference!
					</h2>
				</div>
				<div class="col-12 col-md-6">
					<img class="main d-none d-md-block" src="/images/gold-in-hand-1.png" alt="Image">
					<img class="main d-md-none" src="/images/gold-in-hand-1-m.png" alt="Image">
				</div>
				<div class="col-12 col-md-6 col-lg-5 offset-lg-1">
					<ul>
						<li>
							<strong>Fast, safe, secure, and discrete</strong>
							way to sell
							your gold jewelry and precious metals online.
						</li>
						<li>
							<strong>Satisfaction Guaranteed:</strong>
							You will get your
							items back SAFE &amp; FREE if not satisfied.
						</li>
						<li>
							<strong>Price Match Guarantee*:</strong>
							We match competitor’s
							prices.
						</li>
						<li>
							<strong>Same Day Payments:</strong>
							Get cash for gold within 24 hours of offer approval.
						</li>
						<div id="showMore" :class="{ hide: !isExpanded }">
							<li>
								<strong>Completely FREE Process:</strong>
								No risk. No
								obligation.
							</li>
							<li>
								<strong>100% Transparent:</strong>
								Detailed offer report for
								your gold jewelry or precious metals bullion.
							</li>
							<li>
								<strong>Insured FedEx Shipping:</strong>
								Insured up to $100k
								upon request.
							</li>
							<li>
								<strong>Trusted Buyer:</strong>
								Trusted by thousands for the
								highest payouts and our high standards.
							</li>
						</div>
						<a id="toggleButton" class="d-md-none" href="javascript:void(0);" @click.prevent="toggle">
							<span id="toggleButtonText">{{ isExpanded ? 'Show Less' : 'Show More' }}</span>
							<img src="/images/chevron-icon.svg" alt="Icon">
						</a>
					</ul>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref } from 'vue'
const isExpanded = ref(false)
function toggle() {
	isExpanded.value = !isExpanded.value
}
</script>


