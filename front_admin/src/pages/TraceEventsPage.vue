<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Trace Events" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="row">
								<div class="col-md-4 col-lg-3 col-xl-2">
									<TraceEventsFilters
										v-model="filters"
										:available-event-types="availableEventTypes"
										:available-sources="availableSources"
										:loading="isLoading"
										@reset="resetFilters"
										@filter="loadData"
									/>
								</div>

								<div class="col-md-8 col-lg-9 col-xl-10 pt-3">
									<TraceEventsChart :events="events" :loading="isLoading" />
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
import TraceEventsFilters from '@/components/trace/TraceEventsFilters.vue'
import TraceEventsChart from '@/components/trace/TraceEventsChart.vue'
import { queryTraceEvents } from '@/api/traceEvents'
import { getTraceFilterOptions } from '@/api/campaignStats'
import { useToast } from '@/composables/useToast'
import { getDefaultDateRange, buildQueryParams } from '@/utils/traceEvents'

const toast = useToast()

const isLoading = ref(false)
const defaultDates = getDefaultDateRange()

const filters = ref({
	eventType: '',
	source: '',
	campaign: '',
	medium: '',
	term: '',
	content: '',
	dateFrom: defaultDates.dateFrom,
	dateTo: defaultDates.dateTo,
})

const availableEventTypes = ref([])
const availableSources = ref([])
const events = ref([])

const loadData = async () => {
	isLoading.value = true
	try {
		const params = buildQueryParams(filters.value)
		const response = await queryTraceEvents(params)

		if (response.data) {
			events.value = response.data.events || []
			availableEventTypes.value = response.data.availableEventTypes || []

			if (response.data.appliedFilters) {
				const applied = response.data.appliedFilters
				if (applied.eventType) filters.value.eventType = applied.eventType
				if (applied.source) filters.value.source = applied.source
				if (applied.campaign) filters.value.campaign = applied.campaign
				if (applied.medium) filters.value.medium = applied.medium
				if (applied.term) filters.value.term = applied.term
				if (applied.content) filters.value.content = applied.content
				if (applied.dateFrom) filters.value.dateFrom = applied.dateFrom
				if (applied.dateTo) filters.value.dateTo = applied.dateTo
			}
		}
	} catch (error) {
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load trace events'
		toast.error(errorMessage)
	} finally {
		isLoading.value = false
	}
}

const resetFilters = () => {
	const defaultDates = getDefaultDateRange()
	filters.value = {
		eventType: '',
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
