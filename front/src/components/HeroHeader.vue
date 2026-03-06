<template>
	<header class="section-hero">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-12 col-lg-6 col-xl-7">
					<h1>
						<b style="font-weight:900">17% More Cash For Gold.</b>
					</h1>
					<h1 class="hero-subtitle">Sell Gold For the Best Price!</h1>
					<a class="btn btn-primary mb-5" id="getKitBtnMobile"
						href="/#reguestKitFormMobile">Get Paid Today</a>
					<div style="display:none;">
						<a id="video-shortcut" href="/#video-hidden-anchor">
							<img src="/images/video-play-icon.png">
							<span>How it works</span>
						</a>
					</div>
					<ul id="hero-bullet-points">
						<li>
							<span class="bullet mr-2 color-gold">
								<span class="bullet-text">1</span>
							</span>
							Request Free Appraisal Kit
						</li>
						<li>
							<span class="bullet mr-2 color-gold">
								<span class="bullet-text">2</span>
							</span>
							Ship Your Valuables
						</li>
						<li>
							<span class="bullet mr-2 color-gold">
								<span class="bullet-text">3</span>
							</span>
							Get Paid
						</li>
					</ul>
					<div class="d-flex justify-content-start">
						<div class="accreditated-business-medal">
							<div class="mb-3">
								<a href="https://www.bbb.org/us/wa/vancouver/profile/gold-buyers/gold-to-cash-1296-1000129797"
									target="_blank" style="opacity: 0.5;">
									<img src="/images/medal-light-gray.png" style="max-width:90px">
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-12 col-lg-6 col-xl-5">
					<KitForm />
				</div>
			</div>
		</div>
	</header>
</template>

<script setup>
import KitForm from '@/components/forms/KitForm.vue'
</script>


