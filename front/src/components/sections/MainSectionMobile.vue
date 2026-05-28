<template>
    <div>
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
            <h2 class="mobile-testimonial-heading">
                Customers like<br>
                you talk about<br>
                <span class="mobile-testimonial-heading-accent">Gold to Cash</span>
            </h2>
            <TestimonialsSection />
            <div class="p-3">
                <button type="button" class="btn btn-green btn-kit w-100 mt-3" @click="scrollToTop">
                    Get My Free Kit
                </button>
            </div>
        </div>
        <div class="p-3 mt-5 muted-bg">
            <MobileWhatWePay />
                </div>
    </div>
</template>

<script setup>
function scrollToTop() {
	window.scrollTo({ top: 0, behavior: 'smooth' })
}
import MobileSophisticatedForm from '@/components/MobileSophisticatedForm.vue'
import AsSeenIn from '@/components/AsSeenIn.vue'
import TestimonialsSection from '@/components/TestimonialsSection.vue'
import MobileBbbSection from '@/components/MobileBbbSection.vue'
import MobileSellReasons from '@/components/MobileSellReasons.vue'
import MobileWhatWePay from '@/components/MobileWhatWePay.vue'
</script>

<style scoped>
.pt-7 { padding-top: 55px; }
.pt-30 { padding-top: 30px; }
.img-full { width: 100%; }
.fedex-img { width: 100%; max-width: 125px; margin: 0; }
.muted-bg { background-color: #F5F1E7; }
</style>