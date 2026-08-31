<template>
	<section class="wsa-trusted" aria-labelledby="wsa-trusted-title">
		<div class="wsa-trusted__inner">
			<h2 id="wsa-trusted-title" class="wsa-trusted__title">{{ title }}</h2>
			<p class="wsa-trusted__text">
				With our <strong>Gold Standard Service &amp; Pricing</strong> - you’ll see why thousands of satisfied
				customers across the U.S. trust Gold to Cash to turn their gold… into cash!
			</p>
		</div>
	</section>
</template>

<script setup>
/** Figma 819:1717 — section.calc_pay Trusted Nationwide */
const title = 'Gold To Cash is\nTrusted Nationwide'
</script>

<style scoped>
/* Figma 819:1717 — cream band, gold 48px title, black 20px body */
.wsa-trusted {
	width: 100%;
	background: #fff4d3;
	padding: 100px 0 60px;
	box-sizing: border-box;
}

.wsa-trusted__inner {
	max-width: 1228px;
	margin: 0 auto;
	padding: 30px 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 25px;
	text-align: center;
}

.wsa-trusted__title {
	margin: 0;
	max-width: 1226px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 56px;
	color: #c39e3d;
	text-align: center;
	white-space: pre-line;
}

.wsa-trusted__text {
	margin: 0;
	max-width: 1228px;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 400;
	font-size: 20px;
	line-height: 26px;
	color: #000;
	text-align: center;
}

.wsa-trusted__text strong {
	font-weight: 700;
}

@media (max-width: 991.98px) {
	.wsa-trusted {
		padding: 64px 0 48px;
	}

	.wsa-trusted__inner {
		padding: 12px 24px;
		gap: 20px;
	}

	.wsa-trusted__title {
		line-height: 1.2;
	}
}
</style>
