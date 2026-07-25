import { createRouter as _createRouter } from 'vue-router'
import Main from '../pages/Main.vue'
import About from '../pages/About.vue'
import seoService from '../services/seoService.js'

const routes = [
	{ path: '/', name: 'home', component: Main },
	{ path: '/about', name: 'about', component: About },
	{ path: '/sign-in', name: 'sign-in', component: () => import('@/pages/auth/SignIn.vue'), meta: { layout: 'AuthLayout', noIndex: true } },
	{ path: '/password/reset', name: 'forgot-password', component: () => import('@/pages/auth/ForgotPassword.vue'), meta: { layout: 'AuthLayout', noIndex: true } },
	{ path: '/password/email', redirect: { name: 'forgot-password' } },
	{ path: '/how-it-works', redirect: '/how-sell-gold' },
	{ path: '/what-we-pay', name: 'what-we-pay', component: () => import('@/pages/WhatWePay.vue') },
	{ path: '/cash-for-gold', name: 'cash-for-gold', component: () => import('@/pages/CashForGold.vue') },
	{
		path: '/gold-calculator',
		name: 'gold-calculator',
		component: () => import('@/pages/GoldCalculator.vue'),
		meta: { faqVariant: 'calculator' },
	},
	{
		path: '/scrap-gold-calculator',
		name: 'scrap-gold-calculator',
		component: () => import('@/pages/GoldCalculator.vue'),
		meta: { faqVariant: 'scrap' },
	},
	{
		path: '/dental-gold-calculator',
		name: 'dental-gold-calculator',
		component: () => import('@/pages/GoldCalculator.vue'),
		meta: { faqVariant: 'dental' },
	},
	{ path: '/what-we-buy', name: 'what-we-buy', component: () => import('@/pages/WhatWeBuy.vue') },
	{ path: '/sell-luxury-watches', name: 'sell-luxury-watches', component: () => import('@/pages/SellLuxuryWatches.vue') },
	{ path: '/sell-gold-jewelry', name: 'sell-gold-jewelry', component: () => import('@/pages/SellGoldJewelry.vue') },
	{ path: '/sell-gold-coins', name: 'sell-gold-coins', component: () => import('@/pages/SellGoldCoins.vue') },
	{ path: '/sell-gold-rings', name: 'sell-gold-rings', component: () => import('@/pages/SellGoldRings.vue') },
	{ path: '/sell-engagement-ring', name: 'sell-engagement-ring', component: () => import('@/pages/SellEngagementRing.vue') },
	{ path: '/sell-wedding-band', name: 'sell-wedding-band', component: () => import('@/pages/SellWeddingBand.vue') },
	{ path: '/why-us', name: 'why-us', component: () => import('@/pages/WhyUs.vue') },
	{ path: '/faq', name: 'faq', component: () => import('@/pages/Faq.vue') },
	{ path: '/contact-us', name: 'contact-us', component: () => import('@/pages/ContactUs.vue') },
	{ path: '/gold-info', name: 'gold-info', component: () => import('@/pages/GoldInfo.vue') },
	{ path: '/sell-gold', name: 'sell-gold', component: () => import('@/pages/SellGold.vue') },
	{ path: '/sell', name: 'sell', component: () => import('@/pages/Sell.vue') },
	{ path: '/terms-and-conditions', name: 'terms-and-conditions', component: () => import('@/pages/TermsAndConditions.vue') },
	{ path: '/privacy-policy', name: 'privacy-policy', component: () => import('@/pages/PrivacyPolicy.vue') },
	{ path: '/sell-gold/gold-to-cash-reviews', name: 'gold-to-cash-reviews', component: () => import('@/pages/GoldToCashReviews.vue') },
	{ path: '/sell-gold/cash-for-gold-insights-for-the-highest-roi', name: 'cash-for-gold-insights', component: () => import('@/pages/CashForGoldInsights.vue') },
	{ path: '/free-gold-appraisal-insured-shipping', name: 'free-gold-appraisal-insured-shipping', component: () => import('@/pages/FreeGoldAppraisalInsuredShipping.vue') },
	{ path: '/online-gold-jewelry-buyer-usa', name: 'online-gold-jewelry-buyer-usa', component: () => import('@/pages/OnlineGoldJewelryBuyerUsa.vue') },
	{ path: '/scrap-gold-buyer-online', name: 'scrap-gold-buyer-online', component: () => import('@/pages/ScrapGoldBuyerOnline.vue') },
	{ path: '/best-place-sell-gold', name: 'best-place-sell-gold', component: () => import('@/pages/BestPlaceSellGold.vue') },
	{ path: '/gold-buyer-online', name: 'gold-buyer-online', component: () => import('@/pages/GoldBuyerOnline.vue') },
	{ path: '/how-sell-gold', name: 'how-sell-gold', component: () => import('@/pages/HowSellGold.vue') },
	{ path: '/what-sets-apart', name: 'what-sets-apart', component: () => import('@/pages/WhatSetsApart.vue') },
	{ path: '/how-sell-gold-jewelry-online-usa', name: 'how-sell-gold-jewelry-online-usa', component: () => import('@/pages/HowSellGoldJewelry.vue') },
	{ path: '/gold-info/:slug', name: 'gold-info-post', component: () => import('@/pages/BlogPost.vue') },
	{ path: '/sell/:slug', name: 'sell-article', component: () => import('@/pages/BlogPost.vue') },
	{ path: '/sell-gold/:slug', name: 'sell-gold-post', component: () => import('@/pages/BlogPost.vue') },
	{ path: '/user/kit-request-success', name: 'kit-request-success', component: () => import('@/pages/account/KitRequestSuccess.vue'), meta: { requiresAuth: true } },
	{ path: '/user/account', name: 'user-account', component: () => import('@/pages/account/UserAccount.vue'), meta: { requiresAuth: true } },
	{ path: '/:pathMatch(.*)*', name: 'not-found', component: () => import('@/pages/NotFound.vue'), meta: { statusCode: 404 } }
]

export function getStaticSitemapPaths() {
	return routes
		.filter((r) => !r.path.includes(':') && !r.meta?.requiresAuth && !r.meta?.noIndex && !r.redirect)
		.map((r) => r.path)
}

export function createRouter(history) {
	const router = _createRouter({
		history,
		routes,
	})

	router.beforeEach((to, from, next) => {
		if (to?.meta?.requiresAuth) {
			if (typeof window === 'undefined') {
				next()
				return
			}

			const token = localStorage.getItem('jwt_token')

			if (!token) {
				next({
					name: 'sign-in',
					query: { redirect: to.fullPath }
				})
			} else {
				next()
			}
		} else {
			next()
		}
	})

	router.afterEach((to) => {
		seoService.loadSeoForRoute(to)
	})

	return router
}


