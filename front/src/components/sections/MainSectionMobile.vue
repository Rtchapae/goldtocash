<template>
    <div>
        <div class="">
            <MobileSophisticatedForm />
        </div>
        <LazyBelowFold :min-height="80" class="mt-5 px-3">
            <AsSeenIn />
        </LazyBelowFold>
        <div class="mt-5 px-3">
            <div class="text-center">
                <img src="/images/fedex-small-icon.svg" class="fedex-img" alt="" loading="lazy">
                <p class="mt-3">Free &amp; Insured shipping up to $100,000*</p>
            </div>
        </div>
        <LazyBelowFold :min-height="200" class="mt-5 px-3">
            <MobileBbbSection />
        </LazyBelowFold>
        <LazyBelowFold :min-height="480" class="mt-5 px-3">
            <MobileSellReasons />
        </LazyBelowFold>
        <LazyBelowFold id="mobile-testimonial" :min-height="360">
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
        </LazyBelowFold>
        <LazyBelowFold :min-height="320" class="p-3 mt-5 muted-bg">
            <MobileWhatWePay />
        </LazyBelowFold>
    </div>
</template>

<script setup>
import { defineAsyncComponent } from 'vue'
import LazyBelowFold from '@/components/LazyBelowFold.vue'
import MobileSophisticatedForm from '@/components/MobileSophisticatedForm.vue'

const AsSeenIn = defineAsyncComponent(() => import('@/components/AsSeenIn.vue'))
const MobileBbbSection = defineAsyncComponent(() => import('@/components/MobileBbbSection.vue'))
const MobileSellReasons = defineAsyncComponent(() => import('@/components/MobileSellReasons.vue'))
const TestimonialsSection = defineAsyncComponent(() => import('@/components/TestimonialsSection.vue'))
const MobileWhatWePay = defineAsyncComponent(() => import('@/components/MobileWhatWePay.vue'))

function scrollToTop() {
	window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<style scoped>
.pt-7 { padding-top: 55px; }
.pt-30 { padding-top: 30px; }
.img-full { width: 100%; }
.fedex-img { width: 100%; max-width: 125px; margin: 0; }
.muted-bg { background-color: #F5F1E7; }
</style>
