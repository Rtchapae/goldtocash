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
					<ul class="footer-nav">
						<li v-for="link in footerMainLinks" :key="link.href" class="nav-item-footer">
							<a :href="link.href">{{ link.label }}</a>
						</li>
					</ul>
					<ul class="footer-nav footer-nav--resources">
						<li v-for="link in footerResourceLinks" :key="link.href" class="nav-item-footer">
							<a :href="link.href">{{ link.label }}</a>
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
import { footerMainLinks, footerResourceLinks } from '@/constants/whatWeBuyNav'

const sellArticles = ref([])
const currentYear = new Date().getFullYear()

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

.footer-nav {
	display: flex;
	flex-wrap: wrap;
	justify-content: center;
	gap: 12px 36px;
	max-width: 920px;
	margin: 0 auto 20px;
	padding: 0;
	text-align: center;
}

.footer-nav li {
	display: inline-block;
}

.footer-nav li a {
	margin: 0;
	white-space: nowrap;
}

.footer-nav--resources {
	max-width: 980px;
	gap: 10px 28px;
	margin-bottom: 8px;
}

.footer-nav--resources a {
	font-size: 14px;
	line-height: 20px;
	color: rgba(255, 255, 255, 0.78);
}

.footer-nav--resources a:hover {
	color: #fff;
}
</style>
