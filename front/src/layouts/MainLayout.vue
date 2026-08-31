<template>
	<div>
		<Preloader :show="showPreloader" />
		<div class="app-shell" :class="[{ 'is-hidden': showPreloader }, pageClass]" >
            <TopBanner />
            <component :is="isAccountPage ? AccountNavbar : SiteNavbar" />
			<GoldCalculatorHero v-if="isGoldCalculatorPage" />
			<main>
				<slot />
			</main>
            <BenefitsSection />
            <CompaniesStrip />
			<SiteFooter />
		</div>
        <SendKitFormModal v-if="showKitModal" @close="showKitModal = false" />
		<ToastContainer />
	</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, provide, computed, watch, inject, defineAsyncComponent } from 'vue'
import { useRoute } from 'vue-router'
import SiteFooter from '@/components/SiteFooter.vue'
import TopBanner from '@/components/TopBanner.vue'
import SiteNavbar from '@/components/SiteNavbar.vue'
import AccountNavbar from '@/components/AccountNavbar.vue'
import CompaniesStrip from '@/components/CompaniesStrip.vue'
import BenefitsSection from '@/components/BenefitsSection.vue'
import Preloader from '@/components/Preloader.vue'
import ToastContainer from '@/components/ToastContainer.vue'
import GoldCalculatorHero from '@/components/GoldCalculatorHero.vue'

const SendKitFormModal = defineAsyncComponent(() => import('@/components/modals/SendKitFormModal.vue'))

const VUE_SWIPER_CLASSES = [
	'calc-reviews-swiper',
	'gc-figma-372-403-swiper',
	'testimonial-swiper',
	'reviews-swiper',
	'mobile-payout-swiper',
	'trustpilot-swiper',
	'gc-gold-types-swiper'
]

const ssrIsMobile = inject('ssrIsMobile', false)
const showPreloader = ref(!ssrIsMobile)
const showKitModal = ref(false)

const route = useRoute()
const isAccountPage = computed(() => route.path.startsWith('/user'))
const isGoldCalculatorPage = computed(() =>
	['gold-calculator', 'scrap-gold-calculator', 'dental-gold-calculator'].includes(route.name)
)
const is404 = computed(() => route.name === 'not-found')
const pageClass = computed(() => {
	if (route.path === '/') return 'page-home'
	if (is404.value) return 'page-404'
	return 'page-not-home'
})

function openKitModal() { showKitModal.value = true }
function closeKitModal() { showKitModal.value = false }

const openAccountKitRequest = ref(null)
provide('openAccountKitRequest', openAccountKitRequest)

provide('openKitModal', openKitModal)
provide('closeKitModal', closeKitModal)

watch(() => route.fullPath, () => { closeKitModal() })

let swiperBundle = null

async function loadSwiperBundle() {
	if (swiperBundle) return swiperBundle
	const [{ default: Swiper }, modules] = await Promise.all([
		import('swiper'),
		import('swiper/modules')
	])
	swiperBundle = {
		Swiper,
		Navigation: modules.Navigation,
		Pagination: modules.Pagination,
		Autoplay: modules.Autoplay
	}
	return swiperBundle
}

async function initSwipers() {
	const { Swiper, Navigation, Pagination, Autoplay } = await loadSwiperBundle()
	document.querySelectorAll('.swiper').forEach((container) => {
		if (VUE_SWIPER_CLASSES.some((cls) => container.classList.contains(cls))) {
			return
		}
		const paginationEl = container.querySelector('.swiper-pagination')
		const nextEl = container.querySelector('.swiper-next-btn')
		const prevEl = container.querySelector('.swiper-prev-btn')
		try {
			new Swiper(container, {
				modules: [Navigation, Pagination, Autoplay],
				loop: true,
				slidesPerView: 1,
				spaceBetween: 16,
				autoplay: { delay: 4000, disableOnInteraction: false },
				navigation: nextEl && prevEl ? { nextEl, prevEl } : undefined,
				pagination: paginationEl ? { el: paginationEl, clickable: true } : undefined,
				breakpoints: {
					576: { slidesPerView: 2 },
					768: { slidesPerView: 3 },
					1200: { slidesPerView: 4 }
				}
			})
		} catch (_) {
		}
	})
}

function scheduleInitSwipers() {
	const idle = window.requestIdleCallback || ((cb) => setTimeout(cb, 300))
	idle(() => { initSwipers() })
}

onMounted(() => {
	if (typeof window !== 'undefined') {
		window.addEventListener('open-kit-modal', openKitModal)
		window.addEventListener('gtc-lazy-mounted', scheduleInitSwipers)
	}
	const mobile = window.matchMedia('(max-width: 767px)').matches
	if (mobile) {
		showPreloader.value = false
		requestAnimationFrame(() => {
			window.dispatchEvent(new CustomEvent('gtc-app-ready'))
		})
		return
	}
	setTimeout(() => {
		showPreloader.value = false
		requestAnimationFrame(() => {
			if (typeof window !== 'undefined') {
				window.dispatchEvent(new CustomEvent('gtc-app-ready'))
			}
			scheduleInitSwipers()
		})
	}, 120)
})

onBeforeUnmount(() => {
	if (typeof window !== 'undefined') {
		window.removeEventListener('open-kit-modal', openKitModal)
		window.removeEventListener('gtc-lazy-mounted', scheduleInitSwipers)
	}
})
</script>

<style scoped>
.app-shell.is-hidden {
	visibility: hidden;
}
</style>
