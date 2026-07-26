<template>
	<section class="wwp-steps" aria-labelledby="wwp-steps-title">
		<div class="wwp-steps__inner">
			<h2 id="wwp-steps-title" class="wwp-steps__title">
				Let’s get you an offer on your gold! All you have to do are these 3 easy steps:
			</h2>
			<ol class="wwp-steps__list" role="list">
				<li v-for="step in steps" :key="step.num" class="wwp-steps__card">
					<span class="wwp-steps__bg-num" aria-hidden="true">{{ step.num }}</span>
					<h3 class="wwp-steps__card-title">{{ step.title }}</h3>
					<p class="wwp-steps__card-text">{{ step.body }}</p>
				</li>
			</ol>
		</div>
	</section>
</template>

<script setup>
/** Figma 1020:3465 / 1220:1766 — 3 easy steps */
const steps = [
	{
		num: '1',
		title: 'Request a Kit',
		body: 'it’s free and there is zero commitment',
	},
	{
		num: '2',
		title: 'Ship your Gold',
		body: 'insured and tracked every step of the way',
	},
	{
		num: '3',
		title: 'Get an Offer',
		body: 'have our trained specialists give you the most accurate, best offer available to exactly what your jewelry is worth, quickly!',
	},
]
</script>

<style scoped>
.wwp-steps {
	width: 100%;
	background: #fff;
	padding: 80px 0 100px;
	box-sizing: border-box;
}

.wwp-steps__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 48px;
}

.wwp-steps__title {
	margin: 0;
	max-width: 900px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.5rem, 3vw, 36px);
	line-height: 1.25;
	text-align: center;
	color: #000;
}

.wwp-steps__list {
	list-style: none;
	margin: 0 auto;
	padding: 0;
	width: max-content;
	max-width: 100%;
	display: flex;
	flex-direction: row;
	justify-content: center;
	flex-wrap: wrap;
	gap: 24px;
}

.wwp-steps__card {
	position: relative;
	margin: 0;
	width: 260px;
	height: 260px;
	padding: 28px 24px 28px 28px;
	background: #000;
	border-radius: 16px;
	box-shadow: 8px 8px 0 0 #c39e3d;
	box-sizing: border-box;
	overflow: hidden;
	display: flex;
	flex-direction: column;
	justify-content: center;
	align-items: flex-start;
	gap: 12px;
	flex: 0 0 auto;
	text-align: left;
}

.wwp-steps__bg-num {
	position: absolute;
	top: 50%;
	right: 16px;
	transform: translateY(-50%);
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 160px;
	line-height: 1;
	color: rgba(255, 255, 255, 0.14);
	pointer-events: none;
}

.wwp-steps__card-title {
	position: relative;
	z-index: 1;
	margin: 0;
	max-width: 180px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: 22px;
	line-height: 1.2;
	color: #fff;
	text-align: left;
}

.wwp-steps__card-text {
	position: relative;
	z-index: 1;
	margin: 0;
	max-width: 180px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 14px;
	line-height: 1.4;
	color: #fff;
	text-align: left;
}

@media (max-width: 991.98px) {
	.wwp-steps {
		padding: 56px 0 64px;
	}

	.wwp-steps__list {
		flex-direction: column;
		align-items: center;
		width: 100%;
		gap: 20px;
	}

	.wwp-steps__card {
		width: 260px;
		height: 260px;
	}
}
</style>
