<template>
	<nav
		class="navbar navbar-expand-lg"
		:class="{
			'navbar-dark': isScrolled,
			'navbar-dark-always': !isHome,
			'navbar--with-banner-offset': !isAccountPage && !isScrolled
		}"
		:id="isHome ? 'main-navbar' : null"
	>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive"
			aria-controls="navbarResponsive" :aria-expanded="isMenuOpen" aria-label="Toggle navigation" @click.prevent="isMenuOpen = !isMenuOpen">
			<span>
				<img src="/images/menu-icon.svg" alt="Icon">
			</span>
		</button>
		<a class="navbar-brand mx-auto" href="/">
			<img src="/images/logo/white.svg" alt="Gold to Cash" class="d-none d-md-block" width="240">
			<img v-show="isScrolled" src="/images/logo/white.svg" alt="Gold to Cash" class="d-md-none scrolled" width="240">
			<img v-show="!isScrolled" src="/images/logo/black.svg" alt="Gold to Cash" class="d-md-none not-scrolled" width="240">
			<img src="/images/logo/black.svg" alt="Gold to Cash" class="d-none dark" width="240">
		</a>
		<div class="navbar-phone">
			<a href="tel:5642377332">
				<img src="/images/phone-call-white-icon.svg" alt="Icon">
			</a>
		</div>
		<div class="collapse navbar-collapse" :class="{ show: isMenuOpen }" id="navbarResponsive">
			<button class="navbar-toggler closeMenu" type="button" data-bs-toggle="collapse"
				data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" :aria-expanded="isMenuOpen"
				aria-label="Toggle navigation" @click.prevent="isMenuOpen = false">
				<span>
					<img src="/images/close-icon.svg" class="close-icon" alt="Icon">
				</span>
			</button>
			<ul class="navbar-nav ms-auto align-items-lg-center">
				<button type="button" class="btn btn-primary d-lg-none" @click.prevent="openKitModal && openKitModal()">
					Request Free Kit
				</button>
				<li class="nav-item">
					<a href="/how-it-works">How It Works</a>
				</li>
				<template v-for="menu in menus" :key="menu.key">
					<li class="nav-item d-none d-lg-inline-block" style="position:relative;"
						data-toggle="nav-dropdown-menu-desktop"
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
						<a data-toggle="nav-dropdown-menu-mobile" @click.prevent="openMobile[menu.key] = !openMobile[menu.key]">
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
				<a href="/gold-calculator">
					Gold Calculator
				</a>
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
					<img src="/images/phone-call-icon.svg" alt="Icon">
					<a href="tel:5642377332">(564) 237-7332</a>
					<br>
					<span>(Mon - Fri, 9am - 5pm PST)</span>
				</p>
				<p class="getInTouch d-lg-none">
					<img src="/images/mail-icon.svg" alt="Icon">
					<a href="mailto:hello@goldtocash.us">hello@goldtocash.us</a>
				</p>
			</ul>
		</div>
		<a class="d-none d-lg-block btn-secondary" href="/#getStartedForm" @click.prevent="openKitModal && openKitModal()">
			Request Free Kit
		</a>
	</nav>
</template>


<script setup>
import { onMounted, onBeforeUnmount, ref, computed, inject, reactive, watch } from 'vue'
import { useRoute } from 'vue-router'

const isMenuOpen = ref(false)
const route = useRoute()
const isHome = computed(() => route.path === '/')
const isAccountPage = computed(() => route.path.startsWith('/user'))
const isScrolled = ref(false)
let handleScroll = null

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
	{ label: 'Gold info', href: '/gold-info' },
	{ label: 'Sell Gold', href: '/sell-gold' },
	{ label: 'Terms and Conditions', href: '/terms-and-conditions' },
	{ label: 'Privacy Policy', href: '/privacy-policy' }
]

const openKitModal = inject('openKitModal')

onMounted(() => {
	handleScroll = () => {
		isScrolled.value = isHome.value ? window.scrollY >= 1 : true
	}

	handleScroll()
	window.addEventListener('scroll', handleScroll, { passive: true })
	window.addEventListener('resize', handleScroll, { passive: true })
})

watch(() => route.path, () => {
	isScrolled.value = isHome.value ? window.scrollY >= 1 : true
})

onBeforeUnmount(() => {
	if (handleScroll) {
		window.removeEventListener('scroll', handleScroll)
		window.removeEventListener('resize', handleScroll)
	}
})
</script>

<style scoped>
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
