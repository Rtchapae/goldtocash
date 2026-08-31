<template>
	<section class="wwp-blog" aria-labelledby="wwp-blog-title">
		<div class="wwp-blog__inner">
			<h2 id="wwp-blog-title" class="wwp-blog__title">Relevant Blog Reference</h2>
			<ul class="wwp-blog__grid" role="list">
				<li v-for="post in posts" :key="post.href" class="wwp-blog__item">
					<a class="wwp-blog__card" :href="post.href">
						<img
							class="wwp-blog__img"
							:src="post.image"
							:alt="post.title"
							width="380"
							height="240"
							loading="lazy"
							decoding="async"
						/>
						<span class="wwp-blog__caption">{{ post.title }}</span>
					</a>
				</li>
			</ul>
		</div>
	</section>
</template>

<script setup>
/** Figma 1248:2386 / 1251:2682 */
const posts = [
	{
		title: 'Gold Vs. Jewelry Price',
		href: '/sell-gold/cash-for-gold-insights-for-the-highest-roi',
		image: '/images/what-we-pay/blog/blog-01-gold-vs-jewelry.webp',
	},
	{
		title: 'Jewelry Appraisal',
		href: '/free-gold-appraisal-insured-shipping',
		image: '/images/what-we-pay/blog/blog-02-jewelry-appraisal.webp',
	},
	{
		title: 'Gold Calculator',
		href: '/gold-calculator',
		image: '/images/what-we-pay/blog/blog-03-gold-calculator.webp',
	},
	{
		title: 'Free Gold Appraisal With Insured Shipping',
		href: '/free-gold-appraisal-insured-shipping',
		image: '/images/what-we-pay/blog/blog-04-free-appraisal.webp',
	},
	{
		title: 'How To Sell Gold Jewelry Online In The USA',
		href: '/how-sell-gold-jewelry-online-usa',
		image: '/images/what-we-pay/blog/blog-05-sell-jewelry.webp',
	},
]
</script>

<style scoped>
.wwp-blog {
	width: 100%;
	background: #fff4d3;
	padding: 80px 0 100px;
	box-sizing: border-box;
}

.wwp-blog__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.wwp-blog__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.75rem, 3.5vw, 40px);
	line-height: 1.2;
	text-align: center;
	color: #000;
}

.wwp-blog__grid {
	list-style: none;
	margin: 0;
	padding: 0;
	width: 100%;
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 28px 24px;
}

.wwp-blog__card {
	display: flex;
	flex-direction: column;
	gap: 12px;
	text-decoration: none;
	color: inherit;
}

.wwp-blog__img {
	display: block;
	width: 100%;
	aspect-ratio: 380 / 240;
	object-fit: cover;
	border-radius: 8px;
	background: #eee;
}

.wwp-blog__caption {
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 600;
	font-size: 16px;
	line-height: 1.3;
	color: #000;
}

@media (max-width: 991.98px) {
	.wwp-blog {
		padding: 56px 0 64px;
	}

	.wwp-blog__grid {
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 20px 16px;
	}
}

@media (max-width: 575.98px) {
	.wwp-blog__grid {
		grid-template-columns: 1fr;
		max-width: 420px;
		margin: 0 auto;
	}
}
</style>
