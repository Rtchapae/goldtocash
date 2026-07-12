<template>
    <MainSectionDesktop v-if="!isMobile" />
    <MainSectionMobile v-else />
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, inject } from 'vue'
import MainSectionDesktop from '@/components/sections/MainSectionDesktop.vue'
import MainSectionMobile from '@/components/sections/MainSectionMobile.vue'

const ssrIsMobile = inject('ssrIsMobile', false)
const isMobile = ref(ssrIsMobile)

function updateIsMobile() {
	if (typeof window === 'undefined') return
	const next = window.matchMedia('(max-width: 767px)').matches
	const changed = next !== isMobile.value
	isMobile.value = next
	if (changed) {
		nextTick(() => {
			window.dispatchEvent(new CustomEvent('gtc-layout-ready'))
		})
	}
}

onMounted(() => {
	updateIsMobile()
	if (typeof window !== 'undefined') {
		window.addEventListener('resize', updateIsMobile)
		nextTick(() => {
			window.dispatchEvent(new CustomEvent('gtc-layout-ready'))
		})
	}
})

onBeforeUnmount(() => {
	if (typeof window !== 'undefined') {
		window.removeEventListener('resize', updateIsMobile)
	}
})
</script>
