<template>
	<section class="section-whysell2">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<h2>Why Sell Your Gold Jewelry and
						<br class="d-none d-lg-block"> Precious Metals Bullion?
					</h2>
					<p class="sub-title">Is&nbsp;it&nbsp;the right time to&nbsp;sell my&nbsp;gold?</p>
				</div>
				<div class="col-12 col-lg-5 offset-lg-1 order-lg-last">
					<img class="d-none d-lg-block main" src="/images/gold-pile-2.png" alt="Image">
					<img class="d-lg-none main" src="/images/gold-pile-2-m.png" alt="Image">
				</div>
				<div class="col-12 col-lg-6 col-lg-6">
					<div class="row">
						<div class="col-12 col-sm-6">
							<img class="icon" src="/images/trending-up-icon.svg" alt="Icon">
							<h4>Historically High Prices</h4>
							<p>Gold is&nbsp;trading at&nbsp;a&nbsp;historically high price and
								it&nbsp;is&nbsp;a&nbsp;good time to&nbsp;take advantage of&nbsp;it.</p>
						</div>
						<div class="col-12 col-sm-6">
							<img class="icon" src="/images/dollar-sign-icon.svg" alt="Icon">
							<h4>Need Funding</h4>
							<p>Short on&nbsp;cash and counting days until your next paycheck? Get paid
								as&nbsp;soon as&nbsp;24&nbsp;hours of&nbsp;accepting our offer.&nbsp;*</p>
						</div>
						<div class="col-12 col-sm-6">
							<img class="icon" src="/images/credit-card-icon.svg" alt="Icon">
							<h4>Pay-off Debt</h4>
							<p>A&nbsp;great way to&nbsp;pay off credit cards and a&nbsp;smart alternative
								to&nbsp;loans. Get the funding you need with no&nbsp;interest charges.</p>
						</div>
						<div class="col-12 col-sm-6 showMore2" :class="{ hide: !isExpanded }">
							<img class="icon" src="/images/ring-icon.svg" alt="Icon">
							<h4>Old and Broken Jewelry</h4>
							<p>You might have broken or&nbsp;unused jewelry collecting dust that
								is&nbsp;worth thousands of&nbsp;dollars. Send it&nbsp;to&nbsp;us for
								a&nbsp;free and no-obligation evaluation.</p>
						</div>
						<div class="col-12 col-sm-6 showMore2" :class="{ hide: !isExpanded }">
							<img class="icon" src="/images/user-x-icon.svg" alt="Icon">
							<h4>Unwanted Memory</h4>
							<p>Sell&nbsp;us your jewelry that reminds of&nbsp;ex&nbsp;relationships and
								brings unwanted memory.</p>
						</div>
						<div class="col-12 col-sm-6 showMore2" :class="{ hide: !isExpanded }">
							<img class="icon" src="/images/travel-icon.svg" alt="Icon">
							<h4>Vacation or&nbsp;Major Upgrade</h4>
							<p>Is&nbsp;it&nbsp;time for a&nbsp;long-awaited vacation or&nbsp;time for
								a&nbsp;pricey upgrade? Your gold jewelry or&nbsp;precious metals might
								be&nbsp;able to&nbsp;fund&nbsp;it.</p>
						</div>
						<a id="toggleButton2" class="d-sm-none" href="javascript:void(0);" @click.prevent="toggle">
							<span id="toggleButtonText2">{{ isExpanded ? 'Show Less' : 'Show More' }}</span>
							<img src="/images/chevron-icon.svg" alt="Icon">
						</a>
					</div>
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


