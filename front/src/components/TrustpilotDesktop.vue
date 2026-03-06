<template>
    <section class="d-none d-md-block section-trustpilot">
        <div class="section-trustpilot-container">
            <Swiper
                class="trustpilot-swiper"
                :modules="modules"
                :loop="true"
                :slides-per-view="3"
                :space-between="16"
                :navigation="navigation"
                :pagination="pagination"
                :autoplay="{ delay: 4000, disableOnInteraction: true }"
                :breakpoints="{
                    0: { slidesPerView: 1 },
                    768: { slidesPerView: 2 },
                    992: { slidesPerView: 3 }
                }"
            >
                <SwiperSlide v-for="(t, i) in testimonials" :key="i">
                    <div class="swiper-card">
                        <div class="swiper-card__img">
                            <svg width="133px" height="25px" viewBox="0 0 512 96" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <g fill-rule="nonzero">
                                        <rect fill="var(--trustpilot-green)" x="0" y="0" width="96" height="96"></rect>
                                        <rect fill="var(--trustpilot-green)" x="104" y="0" width="96" height="96"></rect>
                                        <rect fill="var(--trustpilot-green)" x="208" y="0" width="96" height="96"></rect>
                                        <rect fill="var(--trustpilot-green)" x="312" y="0" width="96" height="96"></rect>
                                        <rect fill="var(--trustpilot-green)" x="416" y="0" width="96" height="96"></rect>
                                        <path d="M48,64.7 L62.6,61 L68.7,79.8 L48,64.7 Z M81.6,40.4 L55.9,40.4 L48,16.2 L40.1,40.4 L14.4,40.4 L35.2,55.4 L27.3,79.6 L48.1,64.6 L60.9,55.4 L81.6,40.4 L81.6,40.4 L81.6,40.4 L81.6,40.4 Z" fill="#FFFFFF"></path>
                                        <path d="M152,64.7 L166.6,61 L172.7,79.8 L152,64.7 Z M185.6,40.4 L159.9,40.4 L152,16.2 L144.1,40.4 L118.4,40.4 L139.2,55.4 L131.3,79.6 L152.1,64.6 L164.9,55.4 L185.6,40.4 L185.6,40.4 L185.6,40.4 L185.6,40.4 Z" fill="#FFFFFF"></path>
                                        <path d="M256,64.7 L270.6,61 L276.7,79.8 L256,64.7 Z M289.6,40.4 L263.9,40.4 L256,16.2 L248.1,40.4 L222.4,40.4 L243.2,55.4 L235.3,79.6 L256.1,64.6 L268.9,55.4 L289.6,40.4 L289.6,40.4 L289.6,40.4 L289.6,40.4 Z" fill="#FFFFFF"></path>
                                        <path d="M360,64.7 L374.6,61 L380.7,79.8 L360,64.7 Z M393.6,40.4 L367.9,40.4 L360,16.2 L352.1,40.4 L326.4,40.4 L347.2,55.4 L339.3,79.6 L360.1,64.6 L372.9,55.4 L393.6,40.4 L393.6,40.4 L393.6,40.4 L393.6,40.4 Z" fill="#FFFFFF"></path>
                                        <path d="M464,64.7 L478.6,61 L484.7,79.8 L464,64.7 Z M497.6,40.4 L471.9,40.4 L464,16.2 L456.1,40.4 L430.4,40.4 L451.2,55.4 L443.3,79.6 L464.1,64.6 L476.9,55.4 L497.6,40.4 L497.6,40.4 L497.6,40.4 L497.6,40.4 Z" fill="#FFFFFF"></path>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <div class="swiper-card__title">{{ t.title }}</div>
                        <div class="swiper-card__review">{{ t.review }}</div>
                        <div class="swiper-card__author">
                            <a href="/sell-gold/gold-to-cash-reviews" target="_blank">{{ t.author }}</a>
                        </div>
                    </div>
                </SwiperSlide>
            </Swiper>

            <div class="d-flex justify-content-between p-2">
                <span class="swiper-nav swiper-prev-btn">
                    <i class="fas fa-arrow-left"></i>
                </span>
                <div class="trustpilot-swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"></div>
                <span class="swiper-nav swiper-next-btn">
                    <i class="fas fa-arrow-right"></i>
                </span>
            </div>
        </div>
    </section>
</template>

<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination, Autoplay } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'

const modules = [Navigation, Pagination, Autoplay]

const navigation = {
    prevEl: '.swiper-prev-btn',
    nextEl: '.swiper-next-btn'
}

const pagination = {
    el: '.trustpilot-swiper-pagination',
    clickable: true
}

const testimonials = [
    { title: 'Very easy to work with...', review: 'Great customer service. Sale was quick and I received th...', author: 'Amanda K' },
    { title: 'I was looking online...', review: 'unlike a lot of other places like this.', author: 'Dan L' },
    { title: 'I had a very good experience...', review: 'I had a very good experience selling my jewelry to Gold t...', author: 'Edward J' },
    { title: 'Gold to Cash is the Best!...', review: 'but I didn\'t want to just give it away. So I sent in my ...', author: 'Lawson' },
    { title: 'I recommend Gold to Cash...', review: 'I recommend Gold to Cash for selling jewelry. Good reputa...', author: 'Michael L' }
]
</script>


