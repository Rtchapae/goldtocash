<template>
	<div class="App">
		<Sidebar />
		<main class="dashboard-main" :class="{ active: isDesktopCollapsed }">
			<router-view :key="route?.fullPath ?? ''" />
		</main>
	</div>
</template>

<script setup>
import { onMounted, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import Sidebar from '@/components/Sidebar.vue'
import { useSidebarToggle } from '@/composables/useSidebarToggle'

const route = useRoute()
const { isDesktopCollapsed } = useSidebarToggle()

const loadScript = (src) => {
	return new Promise((resolve, reject) => {
		const s = document.createElement('script')
		s.src = src
		s.onload = () => resolve()
		s.onerror = (e) => reject(e)
		document.body.appendChild(s)
	})
}

onMounted(async () => {
	await nextTick()
	await new Promise((r) => setTimeout(r, 0))
	try {
		if (!window.__dashboardVendorsInitialized) {
			const waitForElements = (selectors, { timeoutMs = 5000, intervalMs = 50 } = {}) => {
				return new Promise((resolve) => {
					const start = Date.now()
					const check = () => {
						const allReady = selectors.every((sel) => {
							const el = document.querySelector(sel)
							return el && el.clientWidth > 0 && el.clientHeight >= 0
						})
						if (allReady) return resolve(true)
						if (Date.now() - start > timeoutMs) return resolve(true)
						setTimeout(check, intervalMs)
					}
					check()
				})
			}

			window.Apex = Object.assign({}, window.Apex || {}, {
				chart: Object.assign(
					{
						animations: { enabled: false },
						redrawOnWindowResize: false,
						redrawOnParentResize: false
					},
					(window.Apex && window.Apex.chart) || {}
				)
			})

			await waitForElements([
				'#upDownBarchart',
				'#semiCircleGauge',
				'#areaChart',
				'#dailyIconBarChart',
				'#statisticsDonutChart',
				'#world-map',
				'#transactionLineChart'
			])

			await loadScript('/js/app.js')
			window.__dashboardVendorsInitialized = true
		}
	} catch (e) {
		// eslint-disable-next-line no-console
		console.error('Failed to load vendor scripts', e)
	}
})
</script>

<style scoped>
</style>


