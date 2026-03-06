<template>
	<section class="auth bg-base d-flex flex-wrap">
		<div class="auth-left d-lg-block d-none">
			<div class="d-flex align-items-center flex-column h-100 justify-content-center" :style="backgroundStyle">
				<img :src="backgroundSrc" alt="">
			</div>
		</div>
		<div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
			<div class="max-w-464-px mx-auto w-100">
				<slot />
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const isDarkTheme = ref(false)

const backgroundSrc = computed(() => {
	return '/images/background.jpg'
})

const backgroundStyle = computed(() => {
	return {
		'background-color': isDarkTheme.value ? '#000' : '#1a1a1a'
	}
})

onMounted(() => {
	const savedTheme = localStorage.getItem('admin_theme')
	isDarkTheme.value = savedTheme === 'dark'

	const observer = new MutationObserver((mutations) => {
		mutations.forEach((mutation) => {
			if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
				const theme = document.documentElement.getAttribute('data-theme')
				isDarkTheme.value = theme === 'dark'
			}
		})
	})

	observer.observe(document.documentElement, {
		attributes: true,
		attributeFilter: ['data-theme']
	})

	const handleStorageChange = (e) => {
		if (e.key === 'admin_theme') {
			isDarkTheme.value = e.newValue === 'dark'
		}
	}

	window.addEventListener('storage', handleStorageChange)
})
</script>

<style scoped>
</style>





