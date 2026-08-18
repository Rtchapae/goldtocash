<template>
	<!-- Footer -->
	<footer class="footer">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-12">
					<h2>
						Have a Question?
						<br>
						<span class="footer-heading-accent">We're Always Happy to Help!</span>
					</h2>
				</div>
				<div class="col-12 col-md-auto">
					<p>
						<img src="/images/phone-call-icon.svg" alt="Icon">
						<a href="tel:5642377332">(564) 237-7332</a>
						<br class="d-md-none">
						<span>(Mon - Fri, 9am - 5pm PST)</span>
					</p>
				</div>
				<div class="col-12 col-md-auto">
					<p>
						<img src="/images/mail-icon.svg" alt="Icon">
						<a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a>
					</p>
				</div>
				<div class="col-12 mb-5">
					<p>
						<img src="/images/geo.svg" alt="Icon">
						1101 Broadway St. Suit 230A Vancouver, WA 98660
					</p>
				</div>
				<div class="col-12 d-none d-md-block">
					<ul>
						<li class="nav-item">
							<a href="/">Home</a>
						</li>
						<li class="nav-item-footer">
							<a href="/how-sell-gold">How to Sell Gold</a>
						</li>
						<li class="nav-item-footer">
							<a href="/what-we-pay">What We Pay</a>
						</li>
						<li class="nav-item-footer">
							<a href="/what-we-buy">What We Buy</a>
						</li>
						<li class="nav-item-footer">
							<a href="/what-sets-apart">Why Us</a>
						</li>
						<li class="nav-item-footer">
							<a href="/faq">FAQ's</a>
						</li>
						<li class="nav-item-footer">
							<a href="/contact-us">Contact Us</a>
						</li>
					</ul>
					<ul class="legal">
						<li>
							<a href="/gold-info">Gold info</a>
						</li>
						<li>
							<a href="/sell-gold">Sell Gold</a>
						</li>
						<li>
							<a href="/terms-and-conditions">Terms and Conditions</a>
						</li>
						<li>
							<a href="/privacy-policy">Privacy Policy</a>
						</li>
					</ul>
				</div>
				<div class="col-12 footer-seo-links">
					<div class="row justify-content-center">
						<div
							v-for="group in footerSeoGroups"
							:key="group.title"
							class="col-12 col-sm-6 col-lg-4 footer-seo-column"
						>
							<h3 class="footer-seo-title">{{ group.title }}</h3>
							<ul class="footer-seo-list">
								<li v-for="link in group.links" :key="link.href">
									<a :href="link.href">{{ link.label }}</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<div class="col-12">
					<div class="social-links">
						<a href="https://www.tiktok.com/@goldtocash?_t=8hMrAEnIRnW&amp;_r=1">
							<img src="/images/tiktok.png" alt="TikTok">
						</a>
						<a href="https://www.facebook.com/goldtocash.us">
							<img src="/images/fb.png" alt="Facebook">
						</a>
						<a href="https://www.instagram.com/thegoldtocash/">
							<img src="/images/insta.png" alt="Instagram">
						</a>
						<a href="https://twitter.com/TheGoldtoCash">
							<img src="/images/x.png" alt="Twitter">
						</a>
					</div>
				</div>
				<div>
					<div id="links" class="d-flex justify-content-center flex-wrap gap-2 mb-2">
						<a
							v-for="post in sellArticles"
							:key="post.slug"
							:href="`/sell/${post.slug}`"
							target="_self"
						>{{ post.title }}</a>
					</div>
				</div>
				<div class="col-12">
					<p class="my-2 consent-text">
						* Please refer to
						<a href="/terms-and-conditions">Terms and Conditions</a>
						to assure and eliminate, as best as possible, any confusion you may have about our business
						practices.
					</p>
					<p class="my-2 consent-text">
						Licensed and operating under Washington State Chapter 19.60 RCW Secondhand Precious Metals
						Dealer.
					</p>
					<p class="my-3 consent-text">
						Copyright © 2020-{{ currentYear }}
						<a href="/">Gold to Cash</a>
						All Rights Reserved
					</p>
					<a class="toTop d-md-none" href="/#top">To the top
						<img src="/images/chevron-icon.svg" alt="Icon">
					</a>
				</div>
				<div id="mainScriptAction"></div>
			</div>
		</div>
	</footer>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fetchPosts } from '@/api/posts'

const sellArticles = ref([])
const currentYear = new Date().getFullYear()

const footerSeoGroups = [
	{
		title: 'By Jewelry Type',
		links: [
			{ label: 'Sell Gold Necklaces', href: '/sell-gold-necklaces' },
			{ label: 'Sell Gold Bracelets', href: '/sell-gold-bracelets' },
			{ label: 'Sell Gold Earrings', href: '/sell-gold-earrings' },
			{ label: 'Sell Gold Chains', href: '/sell-gold-chains' },
			{ label: 'Sell Gold Charms', href: '/sell-gold-charms' },
			{ label: 'Sell Scrap Gold', href: '/sell-scrap-gold' },
			{ label: 'Sell Gold Jewelry', href: '/sell-gold-jewelry-online' },
		],
	},
	{
		title: 'By Gold Karat',
		links: [
			{ label: 'Sell 10K Gold', href: '/sell-10k-gold' },
			{ label: 'Sell 14K Gold', href: '/sell-14k-gold' },
			{ label: 'Sell 18K Gold', href: '/sell-18k-gold' },
			{ label: 'Sell 22K Gold', href: '/sell-22k-gold' },
			{ label: 'Sell 24K Gold', href: '/sell-24k-gold' },
		],
	},
	{
		title: 'Resources',
		links: [
			{ label: 'How to Sell Gold', href: '/how-sell-gold' },
			{ label: 'Free Appraisal Kit', href: '/free-appraisal-kit' },
			{ label: 'Track My Package', href: '/track-my-package' },
			{ label: 'Safety & Security', href: '/safety-and-security' },
			{ label: 'FAQs', href: '/gold-selling-faq' },
			{ label: 'Reviews', href: '/reviews' },
			{ label: 'Where to Sell Gold', href: '/where-to-sell-gold' },
			{ label: 'Sell Placer Gold', href: '/sell-placer-gold' },
		],
	},
]

onMounted(async () => {
	try {
		const res = await fetchPosts({ path_prefix: '/sell', per_page: 100 })
		sellArticles.value = res?.data ?? []
	} catch {
		sellArticles.value = []
	}
})
</script>

<style scoped>
.footer-heading-accent {
	color: #fff;
	text-align: center;
	font-family: Montserrat, sans-serif;
	font-size: 18px;
	font-style: normal;
	font-weight: 500;
	line-height: 24px;
}

.footer-seo-links {
	margin: 24px 0 16px;
}

.footer-seo-column {
	margin-bottom: 24px;
	text-align: left;
}

.footer-seo-title {
	color: #fff;
	font-family: Montserrat, sans-serif;
	font-size: 14px;
	font-weight: 600;
	line-height: 20px;
	margin: 0 0 12px;
	text-transform: uppercase;
	letter-spacing: 0.04em;
}

.footer-seo-list {
	margin: 0;
	padding: 0;
	text-align: left;
}

.footer-seo-list li {
	display: block;
	margin: 0 0 8px;
}

.footer-seo-list li a {
	margin: 0;
	color: rgba(255, 255, 255, 0.75);
	font-size: 14px;
	line-height: 20px;
}

.footer-seo-list li a:hover {
	color: #fff;
}

@media screen and (max-width: 575px) {
	.footer-seo-column {
		text-align: center;
	}

	.footer-seo-list {
		text-align: center;
	}
}
</style>
