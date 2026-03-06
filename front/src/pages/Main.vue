<template>
    <MainSectionDesktop v-if="!isMobile" />
    <MainSectionMobile v-else />
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import MainSectionDesktop from '@/components/sections/MainSectionDesktop.vue'
import MainSectionMobile from '@/components/sections/MainSectionMobile.vue'

const isMobile = ref(false)

function updateIsMobile() {
    if (typeof window !== 'undefined') {
        isMobile.value = window.innerWidth < 768
    }
}

onMounted(() => {
	updateIsMobile()
	if (typeof window !== 'undefined') {
		window.addEventListener('resize', updateIsMobile)
	}
})

onBeforeUnmount(() => {
	if (typeof window !== 'undefined') {
		window.removeEventListener('resize', updateIsMobile)
	}
})
</script>
