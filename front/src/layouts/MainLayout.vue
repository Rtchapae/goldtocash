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
import { ref, onMounted, onBeforeUnmount, provide, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import Swiper from 'swiper'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'
import SiteFooter from '@/components/SiteFooter.vue'
import SendKitFormModal from '@/components/modals/SendKitFormModal.vue'
import TopBanner from '@/components/TopBanner.vue'
import SiteNavbar from '@/components/SiteNavbar.vue'
import AccountNavbar from '@/components/AccountNavbar.vue'
import CompaniesStrip from '@/components/CompaniesStrip.vue'
import BenefitsSection from '@/components/BenefitsSection.vue'
import Preloader from '@/components/Preloader.vue'
import ToastContainer from '@/components/ToastContainer.vue'
import GoldCalculatorHero from '@/components/GoldCalculatorHero.vue'

const showPreloader = ref(true)
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

onMounted(() => {
	if (typeof window !== 'undefined') {
		window.addEventListener('open-kit-modal', openKitModal)
	}
	setTimeout(() => {
		showPreloader.value = false
		requestAnimationFrame(() => {
			if (typeof window !== 'undefined') {
				window.dispatchEvent(new CustomEvent('gtc-app-ready'))
			}
			document.querySelectorAll('.swiper').forEach((container) => {
				/* swiper/vue управляет сам; иначе двойной new Swiper ломает пагинацию */
				if (
					container.classList.contains('calc-reviews-swiper') ||
					container.classList.contains('gc-figma-372-403-swiper')
				) {
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
		})
	}, 500)
})

onBeforeUnmount(() => {
	if (typeof window !== 'undefined') {
		window.removeEventListener('open-kit-modal', openKitModal)
	}
})
</script>

<style scoped>
.app-shell.is-hidden {
	visibility: hidden;
}
</style>

