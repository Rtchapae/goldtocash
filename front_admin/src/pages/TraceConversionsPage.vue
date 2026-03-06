<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Trace Conversions" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="row">
								<div class="col-lg-4">
									<TraceConversionsFilters
										v-model="filters"
										:available-sources="availableSources"
										:loading="isLoading"
										@reset="resetFilters"
										@filter="loadData"
									/>
								</div>

								<div class="col-lg-8 pt-3">
									<TraceConversionsReport
										:conversions="conversions"
										:is-loading="isLoading"
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
import TraceConversionsFilters from '@/components/trace/TraceConversionsFilters.vue'
import TraceConversionsReport from '@/components/trace/TraceConversionsReport.vue'
import { queryTraceConversions } from '@/api/traceConversions'
import { getTraceFilterOptions } from '@/api/campaignStats'
import { useToast } from '@/composables/useToast'
import { getDefaultDateRange, buildQueryParams } from '@/utils/traceConversions'

const toast = useToast()

const isLoading = ref(false)
const defaultDates = getDefaultDateRange()

const filters = ref({
	source: '',
	campaign: '',
	medium: '',
	term: '',
	content: '',
	dateFrom: defaultDates.dateFrom,
	dateTo: defaultDates.dateTo,
})

const conversions = ref({})
const availableSources = ref([])

const loadData = async () => {
	isLoading.value = true
	try {
		const params = buildQueryParams(filters.value)
		const response = await queryTraceConversions(params)

		if (response.data) {
			conversions.value = response.data.conversions || {}

			if (response.data.appliedFilters) {
				const applied = response.data.appliedFilters
				if (applied.source !== undefined) filters.value.source = applied.source || ''
				if (applied.campaign !== undefined) filters.value.campaign = applied.campaign || ''
				if (applied.medium !== undefined) filters.value.medium = applied.medium || ''
				if (applied.term !== undefined) filters.value.term = applied.term || ''
				if (applied.content !== undefined) filters.value.content = applied.content || ''
				if (applied.dateFrom) filters.value.dateFrom = applied.dateFrom
				if (applied.dateTo) filters.value.dateTo = applied.dateTo
			}
		}
	} catch (error) {
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load trace conversions'
		toast.error(errorMessage)
	} finally {
		isLoading.value = false
	}
}

const resetFilters = () => {
	const defaultDates = getDefaultDateRange()
	filters.value = {
		source: '',
		campaign: '',
		medium: '',
		term: '',
		content: '',
		dateFrom: defaultDates.dateFrom,
		dateTo: defaultDates.dateTo,
	}
	loadData()
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
	loadData()
})
</script>

<style scoped>
</style>
