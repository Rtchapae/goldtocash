<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Campaign Stats" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="row">
								<!-- Filters -->
								<div class="col-lg-5 col-xl-3 mb-3">
									<CampaignStatsFilters
										v-model="filters"
										:available-sources="availableSources"
										:loading="isLoading"
										@filter="loadData"
									/>
								</div>

								<!-- Table -->
								<div class="col-lg-7 col-xl-9">
									<CampaignStatsTable
										:campaign-stats="campaignStats"
										:loading="isLoading"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import CampaignStatsFilters from '@/components/trace/CampaignStatsFilters.vue'
import CampaignStatsTable from '@/components/trace/CampaignStatsTable.vue'
import { queryCampaignStats, getTraceFilterOptions } from '@/api/campaignStats'
import { useToast } from '@/composables/useToast'
import { getDefaultDateRange } from '@/utils/traceEvents'

const toast = useToast()

const isLoading = ref(false)
const defaultDates = getDefaultDateRange()

const filters = ref({
	source: 'pfm',
	dateFrom: defaultDates.dateFrom,
	dateTo: defaultDates.dateTo,
})

const campaignStats = ref([])
const availableSources = ref([])

const loadData = async () => {
	if (!filters.value.source) {
		toast.error('Source is required')
		return
	}

		isLoading.value = true
	try {
		const response = await queryCampaignStats({
			source: filters.value.source,
			dateFrom: filters.value.dateFrom,
			dateTo: filters.value.dateTo,
		})

		const data = response.data || response

		if (data) {
			campaignStats.value = data.campaignStats || []

			if (data.appliedFilters) {
				const applied = data.appliedFilters
				if (applied.source) filters.value.source = applied.source
				if (applied.dateFrom) filters.value.dateFrom = applied.dateFrom
				if (applied.dateTo) filters.value.dateTo = applied.dateTo
			}
		}
	} catch (error) {
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 400
			? error.data?.error || 'Invalid request. Please check your filters.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load campaign stats'
		toast.error(errorMessage)
		campaignStats.value = []
	} finally {
		isLoading.value = false
	}
}

const loadFilterOptions = async () => {
	try {
		const response = await getTraceFilterOptions()
		const data = response.data || response

		if (data) {
			availableSources.value = data.sources || []
		}
	} catch (error) {
		console.error('Failed to load filter options:', error)
		toast.error('Failed to load filter options')
	}
}

onMounted(() => {
	loadFilterOptions()
})
</script>

<style scoped>
/* Styles are in _trace_events_page.scss */
</style>
