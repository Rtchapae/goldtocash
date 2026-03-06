<template>
	<section class="section-whatwebuy page-content">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12">
					<h2>What We Buy</h2>
					<p class="sub-title">
						Gold to Cash is more than just a gold buyer, we buy everything that
						contains gold or other precious metals and pay most money.
					</p>
				</div>
				<div class="col-12 col-lg-6">
					<div class="row">
						<div class="col-12 col-md-6 col-lg-12">
							<p class="unordered-title">Jewelry</p>
							<ul>
								<li>Rings, necklaces, earrings, bracelets, pendants.</li>
								<li>All purities and sizes.</li>
								<li>Gold, silver, platinum.</li>
								<li>New, used, broken and damaged.</li>
								<li>Designer jewelry and luxury watches.</li>
							</ul>
							<p class="unordered-title">Coins</p>
							<ul>
								<li>Any gold, silver and platinum coins.</li>
								<li>Collectible coins.</li>
								<li>Any country and denomination.</li>
							</ul>
						</div>
						<div class="col-12 col-md-6 col-lg-12">
							<p class="unordered-title">Precious Metals Bullion</p>
							<ul>
								<li>Any gold, silver and platinum bullion.</li>
								<li>Bars of any weight.</li>
								<li>Rounds of any weight.</li>
							</ul>
							<div class="row my-md-5 py-md-3 d-lg-none"></div>
							<p class="unordered-title">Gold</p>
							<ul>
								<li>Gold dentures.</li>
								<li>Gold granules.</li>
								<li>Gold nuggets.</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl-5 offset-xl-1">
					<KitForm />
				</div>
				<div class="col-12">
					<p class="extra-content">
						Convert your gold jewelry and precious metals bullion into cash today. If
						you're not sure what to sell, or your item is not within any category
						above, don't hesitate to contact us for more info or a quote.
					</p>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'
</script>

<style scoped>
.page-content {
	padding: 150px 0 0;
}

@media (max-width: 767px) {
	.page-content {
		padding: 50px 0 0;
	}
}
</style>
