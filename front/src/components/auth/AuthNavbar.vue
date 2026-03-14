<template>
	<div class="row w-100">
		<nav class="navbar navbar-expand-lg w-100">
			<button class="navbar-toggler" type="button" data-toggle="collapse"
				data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
				aria-label="Toggle navigation" @click.prevent="isMenuOpen = !isMenuOpen">
				<span>
					<img src="/images/menu-icon.svg" alt="Icon">
				</span>
			</button>
			<a class="navbar-brand me-auto" href="/">
				<img src="/images/logo/white.svg" alt="Gold to Cash" width="240" height="auto" loading="lazy">
			</a>
			<div class="collapse navbar-collapse" :class="{ show: isMenuOpen }" id="navbarResponsive">
				<button class="navbar-toggler closeMenu" type="button" data-toggle="collapse"
					data-target="#navbarResponsive" aria-controls="navbarResponsive"
					aria-expanded="false" aria-label="Toggle navigation" @click.prevent="isMenuOpen = false">
					<span>
						<img src="/images/close-icon.svg" alt="Icon" width="24" height="24" loading="lazy">
					</span>
				</button>
				<ul class="navbar-nav ms-auto align-items-lg-center">
					<button type="button" class="btn btn-primary d-lg-none" @click.prevent="openKitModal && openKitModal()">
						Request Free Kit
					</button>
					<li class="nav-item">
						<a href="/how-it-works">How It Works</a>
					</li>
					<!-- Dropdown menus (data-driven) -->
					<template v-for="menu in menus" :key="menu.key">
						<li class="nav-item d-none d-lg-inline-block nav-item-dropdown"
							@mouseenter="openDesktop[menu.key] = true"
							@mouseleave="openDesktop[menu.key] = false">
							<a :href="menu.href">
								{{ menu.label }}
								<span class="nav-caret ml-1" :class="{ 'nav-caret--up': openDesktop[menu.key] }" aria-hidden="true"></span>
							</a>
							<ul :id="`${menu.key}-dropdown-desktop`" class="nav-dropdown-menu desktop" :class="{ open: openDesktop[menu.key] }">
								<li v-for="item in menu.desktopItems" :key="item.href">
									<a :href="item.href">{{ item.label }}</a>
								</li>
							</ul>
						</li>
						<li class="nav-item d-lg-none">
							<a @click.prevent="openMobile[menu.key] = !openMobile[menu.key]">
								{{ menu.label }}
								<span class="nav-caret ml-1" :class="{ 'nav-caret--up': openMobile[menu.key] }" aria-hidden="true"></span>
							</a>
							<ul :id="`${menu.key}-dropdown-mobile`" class="nav-dropdown-menu mobile" :class="{ open: openMobile[menu.key] }">
								<li v-for="item in menu.mobileItems" :key="item.href">
									<a :href="item.href">{{ item.label }}</a>
								</li>
							</ul>
						</li>
					</template>
					<li class="nav-item">
						<a href="/gold-calculator">Gold Calculator</a>
					</li>
					<li class="nav-item d-none d-lg-inline-block">
						<a href="/why-us">Why Us</a>
					</li>
					<li class="nav-item d-none d-xl-inline-block">
						<a href="/faq">FAQ's</a>
					</li>
					<li class="nav-item d-none d-xl-inline-block">
						<a href="/contact-us">Contact Us</a>
					</li>
					<div class="mobile-links d-lg-none">
						<li class="nav-item" v-for="link in mobileLinks" :key="link.href">
							<a :href="link.href">{{ link.label }}</a>
						</li>
					</div>
					<p class="getInTouch d-lg-none">
						<img src="/images/phone-call-icon.svg" alt="Icon" width="24" height="24" loading="lazy">
						<a href="tel:5642377332" target="_blank">564-237-7332</a>
						<br><span>Mon-Sat, 7am-8pm PST</span>
					</p>
					<p class="getInTouch d-lg-none">
						<img src="/images/mail-icon.svg" alt="Icon" width="24" height="24" loading="lazy">
						<a href="mailto:hello@goldtocash.us" target="_blank">hello@goldtocash.us</a>
					</p>
				</ul>
			</div>
		</nav>
	</div>
</template>

<script setup>
import { ref, reactive, inject } from 'vue'

const isMenuOpen = ref(false)

const menus = [
	{
		key: 'wwp',
		label: 'What We Pay',
		href: '/what-we-pay',
		desktopItems: [
			{ label: 'Cash for Gold', href: '/cash-for-gold' }
		],
		mobileItems: [
			{ label: 'What We Pay', href: '/what-we-pay' },
			{ label: 'Cash for Gold', href: '/cash-for-gold' }
		]
	},
	{
		key: 'wwb',
		label: 'What We Buy',
		href: '/what-we-buy',
		desktopItems: [
			{ label: 'Sell Luxury Watches', href: '/sell-luxury-watches' },
			{ label: 'Sell Your Gold Jewelry', href: '/sell-gold-jewelry' },
			{ label: 'Sell Your Gold Coins', href: '/sell-gold-coins' },
			{ label: 'Sell Your Gold Rings', href: '/sell-gold-rings' },
			{ label: 'Sell Engagement Rings', href: '/sell-engagement-ring' },
			{ label: 'Sell Wedding Band', href: '/sell-wedding-band' }
		],
		mobileItems: [
			{ label: 'What We Buy', href: '/what-we-buy' },
			{ label: 'Sell Luxury Watches', href: '/sell-luxury-watches' },
			{ label: 'Sell Your Gold Jewelry', href: '/sell-gold-jewelry' },
			{ label: 'Sell Your Gold Coins', href: '/sell-gold-coins' },
			{ label: 'Sell Your Gold Rings', href: '/sell-gold-rings' },
			{ label: 'Sell Engagement Rings', href: '/sell-engagement-ring' },
			{ label: 'Sell Wedding Band', href: '/sell-wedding-band' }
		]
	}
]

const openDesktop = reactive({ wwp: false, wwb: false })
const openMobile = reactive({ wwp: false, wwb: false })

const mobileLinks = [
	{ label: 'Gold Info', href: '/gold-info' },
	{ label: 'Terms and Conditions', href: '/terms-and-conditions' },
	{ label: 'Privacy Policy', href: '/privacy-policy' }
]

const openKitModal = inject('openKitModal', null)
</script>

<style scoped>
.nav-item-dropdown {
	position: relative;
}
.nav-caret {
	display: inline-block;
	width: 0;
	height: 0;
	border-left: 5px solid transparent;
	border-right: 5px solid transparent;
	border-top: 5px solid currentColor;
	vertical-align: middle;
	transition: transform 0.2s;
}
.nav-caret--up {
	transform: rotate(-180deg);
}
</style>

