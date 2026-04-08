<template>
	<!-- Figma 38:2165 / Frame 3910 — five filled stars, #DBAF3E, ~12px, gap ~1.55px -->
	<svg
		class="trustpilot-five-stars-svg"
		width="70"
		height="14"
		viewBox="0 0 70 14"
		fill="none"
		xmlns="http://www.w3.org/2000/svg"
		aria-hidden="true"
	>
		<g fill="#DBAF3E">
			<path
				v-for="(x, i) in starXs"
				:key="i"
				:transform="`translate(${x}, ${yPad}) scale(${starScale})`"
				d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
			/>
		</g>
	</svg>
</template>

<script setup>
/** Figma: star ~12.37px, gap ~1.55px → step ≈13.92 in 24×24 space after scale */
const starScale = 12.37 / 24
const step = 12.37 + 1.5459264516830444
const starXs = [0, step, step * 2, step * 3, step * 4]
const yPad = 0.8
</script>

<style scoped>
.trustpilot-five-stars-svg {
	display: block;
	width: 100%;
	height: auto;
	max-width: 133px;
}
</style>
