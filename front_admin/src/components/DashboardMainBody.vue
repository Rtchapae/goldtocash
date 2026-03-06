<template>
	<div class="dashboard-main-body">
		<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-24">
			<div class="d-flex flex-column gap-2">
				<router-link
					:to="{ name: 'orders.create' }"
					class="btn btn-primary d-flex align-items-center gap-2"
				>
					<iconify-icon icon="solar:add-circle-bold"></iconify-icon>
					Add transaction
				</router-link>
				<h6 class="fw-semibold mb-0 mt-3">Online</h6>
			</div>
			<PeriodFilter
				v-model="period"
				:from="from"
				:to="to"
				@change="handlePeriodChange"
			/>
		</div>

		<div class="row gy-4">
			<div class="col-xxl-3 col-sm-6">
				<StatCard
					label="Visitors"
					:value="analytics.online?.visitors || 0"
					icon="flowbite:users-group-solid"
					icon-bg-class="bg-primary-600"
					gradient-class="bg-gradient-start-3"
					:change-percent="analytics.online?.visitorsChangePercent ?? 0"
				/>
			</div>
			<div class="col-xxl-3 col-sm-6">
				<StatCard
					label="Orders"
					:value="analytics.online?.orders || 0"
					icon="solar:wallet-bold"
					icon-bg-class="bg-purple"
					icon-class="text-white text-2xl mb-0"
					gradient-class="bg-gradient-start-2"
					:change-percent="analytics.online?.ordersChangePercent ?? 0"
				/>
			</div>
			<div class="col-xxl-3 col-sm-6">
				<StatCard
					label="Transaction"
					:value="analytics.online?.transactions || 0"
					icon="fa6-solid:file-invoice-dollar"
					icon-bg-class="bg-red"
					icon-class="text-white text-2xl mb-0"
					gradient-class="bg-gradient-start-5"
					:change-percent="analytics.online?.transactionsChangePercent ?? 0"
				/>
			</div>
			<div class="col-xxl-3 col-sm-6">
				<StatCard
					label="Paid amount"
					:value="analytics.online?.paidAmount || 0"
					icon="streamline:bag-dollar-solid"
					icon-bg-class="bg-success-main"
					gradient-class="bg-gradient-start-4"
					is-currency
					:change-percent="analytics.online?.paidAmountChangePercent ?? 0"
				/>
			</div>
		</div>

		<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2 mt-24">
			<h6 class="fw-semibold mb-0">Offline</h6>
		</div>

		<div class="row g-3">
			<div class="col-xxl-6 d-flex flex-column gap-4 p-3">
				<BranchSection
					branch-name="Vancouver"
					:data="analytics.offline?.vancouver"
					:change-percent="analytics.offline?.vancouver?.changePercent ?? 0"
				/>
				<BranchSection
					branch-name="Portland"
					:data="analytics.offline?.portland"
					:change-percent="analytics.offline?.portland?.changePercent ?? 0"
				/>
			</div>
			<div class="col-xxl-6 d-flex flex-column gap-4 border p-3">
				<BranchSection
					branch-name="Offline combined"
					:data="analytics.offlineCombined"
					:change-percent="analytics.offlineCombined?.changePercent ?? 0"
				/>
				<BranchSection
					branch-name="All combined"
					:data="analytics.allCombined"
					:change-percent="analytics.allCombined?.changePercent ?? 0"
				/>
			</div>
		</div>
		
		<div class="row g-3">
			<ChartSlider
				ref="chartSlider1"
				title="Online"
				:titles="chart1Titles"
				:chart-ids="['lineChart1_0', 'lineChart1_1', 'lineChart1_2']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
			<ChartSlider
				ref="chartSlider2"
				title="Offline Vancouver"
				:titles="chart2Titles"
				:chart-ids="['lineChart2_0', 'lineChart2_1']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
			<ChartSlider
				ref="chartSlider3"
				title="Offline Portland"
				:titles="chart3Titles"
				:chart-ids="['lineChart3_0', 'lineChart3_1']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
		</div>

		<div class="row g-3 mt-4">
			<ChartSlider
				ref="barChartSlider1"
				title="Comblo Offline"
				:titles="barChart1Titles"
				:chart-ids="['barChart1_0', 'barChart1_1']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
			<ChartSlider
				ref="barChartSlider2"
				title="Transaction"
				:titles="barChart2Titles"
				:chart-ids="['barChart2_0', 'barChart2_1']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
			<ChartSlider
				ref="barChartSlider3"
				title="Paid Amount"
				:titles="barChart3Titles"
				:chart-ids="['barChart3_0', 'barChart3_1']"
				:is-loading="isLoading"
				:charts-initialized="chartsInitialized"
			/>
		</div>

		<div class="row g-3 mt-4">
			<div class="col-xl-4">
				<div class="card">
					<div class="card-header">
						<h6 class="card-title mb-0">Recent Kit Requests</h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead :class="isDarkTheme ? 'table-dark' : 'table-light'">
									<tr>
										<th class="border-0">User</th>
										<th class="border-0">Amount</th>
										<th class="border-0">Date</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="kit in recentActivities.kit_requests" :key="kit.id">
										<td class="text-truncate">{{ kit.user_name }}</td>
										<td>${{ kit.amount }}</td>
										<td class="text-muted">{{ kit.created_at }}</td>
									</tr>
									<tr v-if="!activitiesLoading && (!recentActivities.kit_requests || recentActivities.kit_requests.length === 0)">
										<td colspan="3" class="text-center text-muted py-3">
											<template v-if="activitiesLoading">Loading...</template>
											<template v-else>No recent kit requests</template>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card">
					<div class="card-header">
						<h6 class="card-title mb-0">Recent Status Changes</h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead :class="isDarkTheme ? 'table-dark' : 'table-light'">
									<tr>
										<th class="border-0">Order</th>
										<th class="border-0">Status</th>
										<th class="border-0">Changed By</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="(status, index) in recentActivities.status_changes" :key="`status-${index}`">
										<td>#{{ status.order_id }}</td>
										<td>
											<span class="badge bg-secondary">{{ status.old_status }}</span>
											<i class="fas fa-arrow-right mx-1"></i>
											<span class="badge bg-primary">{{ status.new_status }}</span>
										</td>
										<td class="text-truncate">{{ status.changed_by }}</td>
									</tr>
									<tr v-if="!activitiesLoading && (!recentActivities.status_changes || recentActivities.status_changes.length === 0)">
										<td colspan="3" class="text-center text-muted py-3">
											<template v-if="activitiesLoading">Loading...</template>
											<template v-else>No recent status changes</template>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

			<div class="col-xl-4">
				<div class="card">
					<div class="card-header">
						<h6 class="card-title mb-0">Recent Offline Transactions</h6>
					</div>
					<div class="card-body p-0">
						<div class="table-responsive">
							<table class="table table-hover mb-0">
								<thead :class="isDarkTheme ? 'table-dark' : 'table-light'">
									<tr>
										<th class="border-0">User</th>
										<th class="border-0">Branch</th>
										<th class="border-0">Amount</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="transaction in recentActivities.offline_transactions" :key="transaction.id">
										<td class="text-truncate">{{ transaction.user_name }}</td>
										<td>{{ transaction.branch }}</td>
										<td>${{ transaction.amount }}</td>
									</tr>
									<tr v-if="!activitiesLoading && (!recentActivities.offline_transactions || recentActivities.offline_transactions.length === 0)">
										<td colspan="3" class="text-center text-muted py-3">
											<template v-if="activitiesLoading">Loading...</template>
											<template v-else>No recent offline transactions</template>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>


<script setup>
import { computed, onMounted, onBeforeUnmount, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useAnalyticsStore } from '@/stores/analytics'
import PeriodFilter from '@/components/filters/PeriodFilter.vue'
import { StatCard, BranchSection, ChartSlider } from '@/components/dashboard'
import { formatCurrency, formatMonthData } from '@/utils/chartFormatters'
import { createLineChartConfig, createBarChartConfig } from '@/utils/chartConfigs'
import { useChartInitialization } from '@/composables/useChartInitialization'
import ApexCharts from 'apexcharts'

const analyticsStore = useAnalyticsStore()
const { analytics, isLoading } = storeToRefs(analyticsStore)

const period = ref('currentmonth')
const from = ref(null)
const to = ref(null)

const recentActivities = ref({
	kit_requests: [],
	status_changes: [],
	offline_transactions: []
})
const activitiesLoading = ref(false)

const chart1Titles = ['Visitors', 'Orders', 'Paid amount']
const chart2Titles = ['Transaction', 'Paid amount']
const chart3Titles = ['Transaction', 'Paid amount']

const charts1 = ref([])
const charts2 = ref([])
const charts3 = ref([])

const barChart1Titles = ['Transaction', 'Paid Amount']
const barChart2Titles = ['Vancouver', 'Portland']
const barChart3Titles = ['Vancouver', 'Portland']

const barCharts1 = ref([])
const barCharts2 = ref([])
const barCharts3 = ref([])

const chartSlider1 = ref(null)
const chartSlider2 = ref(null)
const chartSlider3 = ref(null)
const barChartSlider1 = ref(null)
const barChartSlider2 = ref(null)
const barChartSlider3 = ref(null)



const getChartData = () => {
	const chartsData = analytics.value.charts || {}
	const onlineCharts = chartsData.online || {}
	const vancouverCharts = chartsData.offline?.vancouver || {}
	const portlandCharts = chartsData.offline?.portland || {}
	const offlineCombined = chartsData.offlineCombined || {}
	
	return {
		online: {
			visitors: formatMonthData(onlineCharts.visitors || [], 'sums'),
			orders: formatMonthData(onlineCharts.orders || [], 'total'),
			paidAmount: formatMonthData(onlineCharts.paidAmount || [], 'total')
		},
		vancouver: {
			transactions: formatMonthData(vancouverCharts.transactions || [], 'total'),
			paidAmount: formatMonthData(vancouverCharts.paidAmount || [], 'total')
		},
		portland: {
			transactions: formatMonthData(portlandCharts.transactions || [], 'total'),
			paidAmount: formatMonthData(portlandCharts.paidAmount || [], 'total')
		},
		offlineCombined: {
			transactions: formatMonthData(offlineCombined.transactions || [], 'total'),
			paidAmount: formatMonthData(offlineCombined.paidAmount || [], 'total')
		}
	}
}


const initLineCharts = () => {
	const data = getChartData()
	
	const options1_0 = createLineChartConfig({
		name: 'Visitors',
		data: data.online.visitors.data,
		categories: data.online.visitors.labels,
		color: '#3B82F6'
	})

	const options1_1 = createLineChartConfig({
		name: 'Orders',
		data: data.online.orders.data,
		categories: data.online.orders.labels,
		color: '#10B981'
	})

	const options1_2 = createLineChartConfig({
		name: 'Paid amount',
		data: data.online.paidAmount.data,
		categories: data.online.paidAmount.labels,
		color: '#8B5CF6'
	})

	const options2_0 = createLineChartConfig({
		name: 'Transaction',
		data: data.vancouver.transactions.data,
		categories: data.vancouver.transactions.labels,
		color: '#F59E0B'
	})

	const options2_1 = createLineChartConfig({
		name: 'Paid amount',
		data: data.vancouver.paidAmount.data,
		categories: data.vancouver.paidAmount.labels,
		color: '#EF4444'
	})

	const options3_0 = createLineChartConfig({
		name: 'Transaction',
		data: data.portland.transactions.data,
		categories: data.portland.transactions.labels,
		color: '#06B6D4'
	})

	const options3_1 = createLineChartConfig({
		name: 'Paid amount',
		data: data.portland.paidAmount.data,
		categories: data.portland.paidAmount.labels,
		color: '#EC4899'
	})

	const chart1Refs = chartSlider1.value?.chartRefs || []
	const chart2Refs = chartSlider2.value?.chartRefs || []
	const chart3Refs = chartSlider3.value?.chartRefs || []

	if (!chart1Refs[0] || !chart1Refs[1] || !chart1Refs[2] || !chart2Refs[0] || !chart2Refs[1] || !chart3Refs[0] || !chart3Refs[1]) {
		console.warn('Chart elements not found, skipping initialization')
		return
	}

	charts1.value = [
		new ApexCharts(chart1Refs[0], options1_0),
		new ApexCharts(chart1Refs[1], options1_1),
		new ApexCharts(chart1Refs[2], options1_2)
	]

	charts2.value = [
		new ApexCharts(chart2Refs[0], options2_0),
		new ApexCharts(chart2Refs[1], options2_1)
	]

	charts3.value = [
		new ApexCharts(chart3Refs[0], options3_0),
		new ApexCharts(chart3Refs[1], options3_1)
	]

	charts1.value.forEach(chart => {
		if (chart) chart.render()
	})
	charts2.value.forEach(chart => {
		if (chart) chart.render()
	})
	charts3.value.forEach(chart => {
		if (chart) chart.render()
	})
	
	chartsInitialized.value = true
}

const initBarCharts = () => {
	const data = getChartData()
	
	const barOptions1_0 = createBarChartConfig({
		name: 'Transaction',
		data: data.offlineCombined.transactions.data,
		categories: data.offlineCombined.transactions.labels,
		color: '#3B82F6'
	})

	const barOptions1_1 = createBarChartConfig({
		name: 'Paid Amount',
		data: data.offlineCombined.paidAmount.data,
		categories: data.offlineCombined.paidAmount.labels,
		color: '#10B981'
	})

	const barOptions2_0 = createBarChartConfig({
		name: 'Vancouver',
		data: data.vancouver.transactions.data,
		categories: data.vancouver.transactions.labels,
		color: '#F59E0B'
	})

	const barOptions2_1 = createBarChartConfig({
		name: 'Portland',
		data: data.portland.transactions.data,
		categories: data.portland.transactions.labels,
		color: '#EF4444'
	})

	// Bar Chart 3 - Paid Amount by branch
	const barOptions3_0 = createBarChartConfig({
		name: 'Vancouver',
		data: data.vancouver.paidAmount.data,
		categories: data.vancouver.paidAmount.labels,
		color: '#8B5CF6'
	})

	const barOptions3_1 = createBarChartConfig({
		name: 'Portland',
		data: data.portland.paidAmount.data,
		categories: data.portland.paidAmount.labels,
		color: '#EC4899'
	})

	// Инициализация всех столбчатых графиков
	const barChart1Refs = barChartSlider1.value?.chartRefs || []
	const barChart2Refs = barChartSlider2.value?.chartRefs || []
	const barChart3Refs = barChartSlider3.value?.chartRefs || []

	if (!barChart1Refs[0] || !barChart1Refs[1] || !barChart2Refs[0] || !barChart2Refs[1] || !barChart3Refs[0] || !barChart3Refs[1]) {
		console.warn('Bar chart elements not found, skipping initialization')
		return
	}

	barCharts1.value = [
		new ApexCharts(barChart1Refs[0], barOptions1_0),
		new ApexCharts(barChart1Refs[1], barOptions1_1)
	]

	barCharts2.value = [
		new ApexCharts(barChart2Refs[0], barOptions2_0),
		new ApexCharts(barChart2Refs[1], barOptions2_1)
	]

	barCharts3.value = [
		new ApexCharts(barChart3Refs[0], barOptions3_0),
		new ApexCharts(barChart3Refs[1], barOptions3_1)
	]

	barCharts1.value.forEach(chart => {
		if (chart) chart.render()
	})
	barCharts2.value.forEach(chart => {
		if (chart) chart.render()
	})
	barCharts3.value.forEach(chart => {
		if (chart) chart.render()
	})
}


const handlePeriodChange = ({ period: newPeriod, from: newFrom, to: newTo }) => {
	period.value = newPeriod
	from.value = newFrom
	to.value = newTo
	
	chartsInitialized.value = false
	
	const params = {}
	if (newPeriod) params.period = newPeriod
	if (newFrom) params.from = newFrom
	if (newTo) params.to = newTo
	
	analyticsStore.getAnalytics(params)
}

const chartsInitialized = ref(false)
const isInitialLoad = ref(true)

watch(() => analytics.value.charts, (newCharts, oldCharts) => {
	if (isInitialLoad.value) {
		isInitialLoad.value = false
		return
	}

	if (!newCharts || Object.keys(newCharts).length === 0) {
		return
	}

	chartsInitialized.value = false

	destroyAllCharts()

	setTimeout(() => {
		initLineCharts()
		initBarCharts()
		chartsInitialized.value = true
	}, 100)
}, { deep: true })

watch(() => analytics.value?.recentActivities, (newActivities) => {
	if (newActivities) {
		recentActivities.value = newActivities
		activitiesLoading.value = false
	}
}, { immediate: true })

const destroyAllCharts = () => {
	const allCharts = [
		...charts1.value,
		...charts2.value,
		...charts3.value,
		...barCharts1.value,
		...barCharts2.value,
		...barCharts3.value
	]
	
	allCharts.forEach(chart => {
		try {
			if (chart && typeof chart.destroy === 'function') {
				chart.destroy()
			}
		} catch (e) {
			console.warn('Error destroying chart:', e)
		}
	})
	
	charts1.value = []
	charts2.value = []
	charts3.value = []
	barCharts1.value = []
	barCharts2.value = []
	barCharts3.value = []
	
	const allChartRefs = [
		...(chartSlider1.value?.chartRefs || []),
		...(chartSlider2.value?.chartRefs || []),
		...(chartSlider3.value?.chartRefs || []),
		...(barChartSlider1.value?.chartRefs || []),
		...(barChartSlider2.value?.chartRefs || []),
		...(barChartSlider3.value?.chartRefs || [])
	]
	
	allChartRefs.forEach(el => {
		if (el) {
			el.innerHTML = ''
		}
	})
}

const updateRecentActivities = () => {
	if (analytics.value?.recentActivities) {
		recentActivities.value = analytics.value.recentActivities
		activitiesLoading.value = false
	}
}

onMounted(() => {
	analyticsStore.getAnalytics({ period: period.value }).then(() => {
		setTimeout(() => {
			if (!chartsInitialized.value) {
				initLineCharts()
				initBarCharts()
			}
		}, 200)
	})
})

onBeforeUnmount(() => {
	destroyAllCharts()
})
</script>

