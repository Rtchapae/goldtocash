<template>
	<component :is="layoutComponent">
		<router-view :key="route.fullPath" />
	</component>
	<ToastContainer />
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import AdminLayout from '@/layouts/AdminLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import ToastContainer from '@/components/ui/ToastContainer.vue'

const route = useRoute()

const layoutComponent = computed(() => {
	return route.meta.layout === 'auth' ? AuthLayout : AdminLayout
})
</script>

<style scoped>
</style>

