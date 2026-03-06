<template>
	<div v-if="show" class="preloader-overlay">
		<img src="/images/white.svg" alt="Logo" class="preloader-logo" />
	</div>
</template>

<script setup>
import { defineProps } from 'vue'

defineProps({
	show: {
		type: Boolean,
		required: true
	}
})
</script>

<style scoped>
.preloader-overlay {
	position: fixed;
	inset: 0;
	background: #000;
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 9999;
}

.preloader-logo {
	width: 180px;
	height: auto;
}
</style>

