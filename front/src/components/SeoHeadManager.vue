<template>
</template>

<script setup>
import { watch } from 'vue'
import { useRoute } from 'vue-router'
import seoService from '@/services/seoService.js'

const route = useRoute()

watch(
	() => route.path,
	async (newPath, oldPath) => {
		if (newPath !== oldPath && typeof window !== 'undefined') {
			await seoService.loadSeoForRoute(route)
		}
	},
	{ immediate: false }
)
</script>
