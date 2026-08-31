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
						<a href="/how-sell-gold">How to Sell Gold</a>
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
							<NavDropdownItems
								:id="`${menu.key}-dropdown-desktop`"
								variant="desktop"
								:items="menu.desktopItems"
								:open="openDesktop[menu.key]"
							/>
						</li>
						<li class="nav-item d-lg-none">
							<a @click.prevent="openMobile[menu.key] = !openMobile[menu.key]">
								{{ menu.label }}
								<span class="nav-caret ml-1" :class="{ 'nav-caret--up': openMobile[menu.key] }" aria-hidden="true"></span>
							</a>
							<NavDropdownItems
								:id="`${menu.key}-dropdown-mobile`"
								variant="mobile"
								:items="menu.mobileItems"
								:open="openMobile[menu.key]"
							/>
						</li>
					</template>
					<li class="nav-item d-none d-lg-inline-block">
						<a href="/what-sets-apart">Why Us</a>
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
import NavDropdownItems from '@/components/NavDropdownItems.vue'
import { whatWeBuyNavItems } from '@/constants/whatWeBuyNav'

const isMenuOpen = ref(false)

const menus = [
	{
		key: 'wwp',
		label: 'What We Pay',
		href: '/what-we-pay',
		desktopItems: [
			{ label: 'Cash for Gold', href: '/cash-for-gold' },
			{ label: 'Free Gold Appraisal with Insured Shipping', href: '/free-gold-appraisal-insured-shipping' },
			{ label: 'Scrap Gold Buyer Online', href: '/scrap-gold-buyer-online' },
			{ label: 'Online Gold Jewelry Buyer in the USA', href: '/online-gold-jewelry-buyer-usa' },
			{ label: 'Best Place to Sell Gold Online in the USA', href: '/best-place-sell-gold' },
			{ label: 'Gold Buyer Online', href: '/gold-buyer-online' },
			{ label: 'How to Sell Gold', href: '/how-sell-gold' },
			{ label: 'How to Sell Gold Jewelry Online in the USA', href: '/how-sell-gold-jewelry-online-usa' }
		],
		mobileItems: [
			{ label: 'What We Pay', href: '/what-we-pay' },
			{ label: 'Cash for Gold', href: '/cash-for-gold' },
			{ label: 'Free Gold Appraisal with Insured Shipping', href: '/free-gold-appraisal-insured-shipping' },
			{ label: 'Scrap Gold Buyer Online', href: '/scrap-gold-buyer-online' },
			{ label: 'Online Gold Jewelry Buyer in the USA', href: '/online-gold-jewelry-buyer-usa' },
			{ label: 'Best Place to Sell Gold Online in the USA', href: '/best-place-sell-gold' },
			{ label: 'Gold Buyer Online', href: '/gold-buyer-online' },
			{ label: 'How to Sell Gold', href: '/how-sell-gold' },
			{ label: 'How to Sell Gold Jewelry Online in the USA', href: '/how-sell-gold-jewelry-online-usa' }
		]
	},
		{
			key: 'wwb',
			label: 'What We Buy',
			href: '/what-we-buy',
			desktopItems: whatWeBuyNavItems,
			mobileItems: whatWeBuyNavItems,
		},
		{
			key: 'gcc',
			label: 'Gold Calculator',
			href: '/gold-calculator',
			desktopItems: [
				{ label: 'Gold Calculator', href: '/gold-calculator' },
				{ label: 'Scrap Gold Calculator', href: '/scrap-gold-calculator' },
				{ label: 'Dental Gold Calculator', href: '/dental-gold-calculator' }
			],
			mobileItems: [
				{ label: 'Gold Calculator', href: '/gold-calculator' },
				{ label: 'Scrap Gold Calculator', href: '/scrap-gold-calculator' },
				{ label: 'Dental Gold Calculator', href: '/dental-gold-calculator' }
			]
		}
]

const openDesktop = reactive({ wwp: false, wwb: false, gcc: false })
const openMobile = reactive({ wwp: false, wwb: false, gcc: false })

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

