<template>
	<component :is="currentLayout">
		<router-view />
	</component>
	<SeoHeadManager />
	<RecentPayoutToast />
	</template>

<script setup>
import { computed, markRaw, defineAsyncComponent } from 'vue'
import { useRoute } from 'vue-router'
import MainLayout from '@/layouts/MainLayout.vue'
import AuthLayout from '@/layouts/AuthLayout.vue'
import SeoHeadManager from '@/components/SeoHeadManager.vue'

const RecentPayoutToast = defineAsyncComponent(() => import('@/components/RecentPayoutToast.vue'))
import './styles/main.scss'

const route = useRoute()
const layouts = {
	MainLayout: markRaw(MainLayout),
	AuthLayout: markRaw(AuthLayout),
}
const currentLayout = computed(() => {
	const name = route?.meta?.layout || 'MainLayout'
	return layouts[name] || layouts.MainLayout
})
</script>


