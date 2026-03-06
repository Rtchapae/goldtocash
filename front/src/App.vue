<template>
	<component :is="currentLayout">
		<router-view />
	</component>
	<SeoHeadManager />
	<RecentPayoutToast />
	</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import SeoHeadManager from '@/components/SeoHeadManager.vue'
import RecentPayoutToast from '@/components/RecentPayoutToast.vue'
import './styles/main.scss'

const route = useRoute()
const layouts = { MainLayout, AuthLayout }
const currentLayout = computed(() => {
	if (!route || !route.meta) {
		return MainLayout
	}
	const name = route.meta.layout || 'MainLayout'
	return layouts[name] || MainLayout
})
</script>


