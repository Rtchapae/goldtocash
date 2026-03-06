<template>
	<section class="section-testimonials">
		<div class="section-testimonials-container">
			<h1 class="testimonial-header">Customers like you
				<br>talk about
				<span style="color:var(--primary)">Gold to Cash</span>
			</h1>
			<div class="d-flex flex-wrap">
				<Swiper
					:modules="modules"
					:slides-per-view="slidesPerView"
					:space-between="20"
					:navigation="navigation"
					:pagination="pagination"
					:breakpoints="breakpoints"
					class="testimonial-swiper"
				>
					<SwiperSlide v-for="(t, index) in testimonials" :key="index">
						<div class="swiper-card">
							<div class="testimonial">
								<div class="visual-container" :class="{ 'show-embed': t.isPlaying }" @click="handleThumbnailClick(index)">
									<div class="image-container" v-if="!t.isPlaying">
										<img :src="t.thumbnail" class="testimonial-thumbnail" :alt="t.author">
										<div class="play-button">
											<span></span>
										</div>
										<div class="duration-block">
											<span></span>
											{{ t.duration }}
										</div>
									</div>
									<iframe
										v-else
										:src="t.videoUrl"
										:title="t.author"
										frameborder="0"
										allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
										allowfullscreen
									></iframe>
								</div>
								<div class="testimonial-words">
									<p v-html="t.quote"></p>
									<p>{{ t.author }}</p>
								</div>
							</div>
						</div>
					</SwiperSlide>
				</Swiper>
			</div>
			<div class="testimonial-controls d-flex justify-content-between mt-4 p-2">
				<span class="swiper-nav swiper-prev-btn">
					<i class="fas fa-arrow-left"></i>
				</span>
				<div class="testimonial-swiper-pagination swiper-pagination"></div>
				<span class="swiper-nav swiper-next-btn">
					<i class="fas fa-arrow-right"></i>
				</span>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation, Pagination } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'

const modules = [Navigation, Pagination]

const navigation = {
	prevEl: '.swiper-prev-btn',
	nextEl: '.swiper-next-btn'
}

const pagination = {
	el: '.testimonial-swiper-pagination',
	clickable: true
}

const breakpoints = {
	0: { slidesPerView: 1 },
	768: { slidesPerView: 2 },
	992: { slidesPerView: 3 },
	1200: { slidesPerView: 4 }
}

const slidesPerView = 1

const testimonials = ref([
	{
		thumbnail: '/images/andrew.jpeg',
		videoUrl: 'https://www.youtube.com/embed/mW7ScnYYCmk?autoplay=1&showinfo=0&controls=0',
		duration: '0:26',
		quote: `The turnaround time is super fast for getting your cash and I
highly highly recommend it to all of my friends, all of my
family and I recommend it to you.`,
		author: 'Andrew',
		isPlaying: false
	},
	{
		thumbnail: '/images/tammy.jpeg',
		videoUrl: 'https://www.youtube.com/embed/y2L_ntBDWkw?autoplay=1&showinfo=0&controls=0',
		duration: '0:21',
		quote: `I'm so happy with the entire process. It is so quick and easy
&amp; they offer some of the most competitive rates for your
gold.`,
		author: 'Tammy',
		isPlaying: false
	},
	{
		thumbnail: '/images/denis.jpeg',
		videoUrl: 'https://www.youtube.com/embed/lzm8ZGYbMGY?autoplay=1&showinfo=0&controls=0',
		duration: '0:29',
		quote: `The offer was surprisingly more than I expected so I accepted
and got the money, direct deposit the next morning!`,
		author: 'Denis',
		isPlaying: false
	},
	{
		thumbnail: '/images/tim.jpeg',
		videoUrl: 'https://www.youtube.com/embed/XgLDErnXXGo?autoplay=1&showinfo=0&controls=0',
		duration: '0:33',
		quote: `They hit me with a very solid offer… Customer service was on
point and I definitely recommend them!`,
		author: 'Tim',
		isPlaying: false
	},
	{
		thumbnail: '/images/vanessa.jpeg',
		videoUrl: 'https://www.youtube.com/embed/Y76axhf5xnk?autoplay=1&showinfo=0&controls=0',
		duration: '0:34',
		quote: `Thanks Gold to Cash for a great first experience selling gold
online. I was a bit nervous but everything went smooth- we
stayed in contact, they answered all my text messages. Got an
offer within an hour...`,
		author: 'Vanessa',
		isPlaying: false
	}
])

function handleThumbnailClick(index) {
	testimonials.value.forEach((t, i) => {
		t.isPlaying = i === index
	})
}
</script>


