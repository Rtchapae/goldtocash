<template>
    <div>
        <div>
            <img src="/images/gold-over-cash.png" class="img-full">
        </div>
        <div class="">
            <MobileSophisticatedForm />
        </div>
        <div class="mt-5 px-3">
            <AsSeenIn />
        </div>
        <div class="mt-5 px-3">
            <div class="text-center">
                <img src="/images/fedex-small-icon.svg" class="fedex-img">
                <p class="mt-3">Free &amp; Insured shipping up to $100,000*</p>
            </div>
        </div>
        <div class="mt-5 px-3">
            <MobileBbbSection />
        </div>
        <div class="mt-5 px-3">
            <MobileSellReasons />
        </div>
        <div id="mobile-testimonial">
            <TestimonialsSection />
            <div class="p-3">
                <button type="button" class="btn btn-green btn-kit w-100 mt-3">
                    Get My Free Kit
                </button>
            </div>
        </div>
        <div class="p-3 mt-5 muted-bg">
            <MobileWhatWePay />
                </div>
        <MobilePromoModal v-if="showPromo" @close="showPromo = false" />
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import MobileSophisticatedForm from '@/components/MobileSophisticatedForm.vue'
import AsSeenIn from '@/components/AsSeenIn.vue'
import TestimonialsSection from '@/components/TestimonialsSection.vue'
import MobileBbbSection from '@/components/MobileBbbSection.vue'
import MobileSellReasons from '@/components/MobileSellReasons.vue'
import MobileWhatWePay from '@/components/MobileWhatWePay.vue'
import MobilePromoModal from '@/components/modals/MobilePromoModal.vue'

const showPromo = ref(false)
let promoTimerId = null

onMounted(() => {
	promoTimerId = setTimeout(() => {
		showPromo.value = true
	}, 10000)
})

onBeforeUnmount(() => {
	if (promoTimerId) {
		clearTimeout(promoTimerId)
		promoTimerId = null
	}
})
</script>

<style scoped>
.pt-7 { padding-top: 55px; }
.pt-30 { padding-top: 30px; }
.img-full { width: 100%; }
.fedex-img { width: 100%; max-width: 125px; margin: 0; }
.muted-bg { background-color: #F5F1E7; }
</style>