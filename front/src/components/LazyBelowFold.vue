<template>
	<div ref="root" class="lazy-below-fold">
		<slot v-if="visible" />
		<div v-else class="lazy-below-fold__placeholder" :style="placeholderStyle" aria-hidden="true" />
	</div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'

const props = defineProps({
	minHeight: { type: [Number, String], default: 1 },
	rootMargin: { type: String, default: '240px 0px' }
})

const visible = ref(false)
const root = ref(null)
let observer = null

const placeholderStyle = computed(() => ({
	minHeight: typeof props.minHeight === 'number' ? `${props.minHeight}px` : props.minHeight
}))

onMounted(() => {
	if (typeof window === 'undefined' || !('IntersectionObserver' in window)) {
		visible.value = true
		return
	}

	observer = new IntersectionObserver(
		([entry]) => {
			if (!entry?.isIntersecting) return
			visible.value = true
			observer?.disconnect()
			window.dispatchEvent(new CustomEvent('gtc-lazy-mounted'))
		},
		{ rootMargin: props.rootMargin }
	)

	if (root.value) observer.observe(root.value)
})

onBeforeUnmount(() => {
	observer?.disconnect()
})
</script>
